<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Gallery;
use App\Helpers\Collapse;
use App\Jobs\OptimizeMedia;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
    
    public $previewImage = null;
    public $galleryImages = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load relationships for image display
        $this->record->load(['preview', 'gallery']);
        
        // Helper function to convert database path (/storage/images/...) to Filament format (images/...)
        $convertPathForFilament = function($path) {
            if (empty($path)) return null;
            // Remove /storage/ prefix and return relative path from disk root
            $path = str_replace('/storage/', '', $path);
            $path = ltrim($path, '/');
            return $path ?: null;
        };
        
        // Load existing preview image - FileUpload expects relative path from disk root (images/...)
        if ($this->record->preview && $this->record->preview->image) {
            $convertedPath = $convertPathForFilament($this->record->preview->image);
            $data['preview_image'] = $convertedPath ? [$convertedPath] : [];
        } else {
            $data['preview_image'] = [];
        }
        
        // Load existing gallery images - FileUpload expects relative paths from disk root (images/...)
        $galleryImages = $this->record->gallery()
            ->where('preview', 0)
            ->where('placement', 'gallery')
            ->whereNull('expires_at')
            ->get()
            ->pluck('image')
            ->filter()
            ->map($convertPathForFilament)
            ->filter()
            ->values()
            ->toArray();
        
        $data['gallery_images'] = $galleryImages;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store images temporarily to process after save
        // FileUpload returns array even for single file
        $previewImageData = $data['preview_image'] ?? [];
        $this->previewImage = is_array($previewImageData) && !empty($previewImageData) 
            ? $previewImageData 
            : [];
        
        $galleryImagesData = $data['gallery_images'] ?? [];
        $this->galleryImages = is_array($galleryImagesData) && !empty($galleryImagesData)
            ? $galleryImagesData
            : [];
        
        // Remove images from data as they will be processed separately in afterSave
        unset($data['preview_image'], $data['gallery_images']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record->fresh(['preview', 'gallery']);
        $newPreviewPath = null;
        $newGalleryPaths = [];
        
        // Helper function to normalize image paths for comparison
        $normalizePath = function($path) {
            if (empty($path)) return null;
            // Remove /storage/ prefix and normalize
            $path = str_replace('/storage/', '', $path);
            $path = ltrim($path, '/');
            return $path ?: null;
        };
        
        // Get existing images to compare (normalized paths)
        $existingPreview = $normalizePath($record->preview?->image);
        $existingGallery = $record->gallery()
            ->where('preview', 0)
            ->where('placement', 'gallery')
            ->whereNull('expires_at')
            ->get()
            ->map(function($item) use ($normalizePath) {
                return $normalizePath($item->image);
            })
            ->filter()
            ->values()
            ->toArray();
        
        // Handle preview image
        $previewArray = is_array($this->previewImage) ? $this->previewImage : ($this->previewImage ? [$this->previewImage] : []);
        
        if (empty($previewArray) || (count($previewArray) === 1 && empty($previewArray[0]))) {
            // Preview was removed - mark existing as expired
            if ($existingPreview) {
                Gallery::where('model_id', $record->id)
                    ->where('type', 'products')
                    ->where('preview', 1)
                    ->whereNull('expires_at')
                    ->update(['preview' => 0, 'expires_at' => Carbon::now()]);
            }
            $newPreviewPath = null;
        } else {
            $newPreview = $previewArray[0];
            
            // Check if it's a new file (UploadedFile object)
            if (is_object($newPreview) && method_exists($newPreview, 'store')) {
                // New file uploaded
                // Mark old preview as expired
                if ($existingPreview) {
                    Gallery::where('model_id', $record->id)
                        ->where('type', 'products')
                        ->where('preview', 1)
                        ->whereNull('expires_at')
                        ->update(['preview' => 0, 'expires_at' => Carbon::now()]);
                }
                
                $path = $newPreview->store('images', 'public');
                $savedImagePath = "/storage/$path"; // Full path for database
                
                Gallery::create([
                    'user_id' => $record->user_id,
                    'model_id' => $record->id,
                    'type' => 'products',
                    'image' => $savedImagePath,
                    'preview' => 1,
                    'placement' => 'site',
                    'size' => Collapse::bytesToMegabytes($newPreview->getSize()),
                ]);
                
                OptimizeMedia::dispatch('public', $path);
                
                // Update form with saved path (will be converted to Filament format later)
                $newPreviewPath = $savedImagePath;
            } elseif (is_string($newPreview)) {
                // Existing path - could be Filament format (images/...) or DB format (/storage/images/...)
                // If it's Filament format, convert to DB format for comparison
                if (!str_starts_with($newPreview, '/storage/')) {
                    // Filament format - convert to DB format
                    $newPreviewPath = "/storage/$newPreview";
                } else {
                    // Already DB format
                    $newPreviewPath = $newPreview;
                }
            }
        }
        
        // Handle gallery images
        $galleryArray = is_array($this->galleryImages) ? $this->galleryImages : [];
        
        // Normalize current gallery paths (existing images as strings)
        // Filament may return paths in format images/... or /storage/images/...
        $currentGalleryPaths = [];
        $newGalleryPaths = []; // Initialize array for gallery paths
        foreach ($galleryArray as $image) {
            if (is_string($image)) {
                // Normalize path - convert to DB format if needed
                $dbFormatPath = $image;
                if (!str_starts_with($image, '/storage/')) {
                    // Filament format (images/...) - convert to DB format (/storage/images/...)
                    $dbFormatPath = "/storage/$image";
                }
                $normalized = $normalizePath($dbFormatPath);
                $currentGalleryPaths[] = $normalized;
                $newGalleryPaths[] = $dbFormatPath; // Keep in DB format for saving
            }
        }
        
        // Find images that were removed (exist in DB but not in form)
        $removedImages = array_diff($existingGallery, $currentGalleryPaths);
        
        // Mark removed gallery images as expired
        if (!empty($removedImages)) {
            // Convert normalized paths back to full paths for database query
            $fullRemovedPaths = array_map(function($path) {
                return "/storage/$path";
            }, $removedImages);
            
            Gallery::where('model_id', $record->id)
                ->where('type', 'products')
                ->where('preview', 0)
                ->where('placement', 'gallery')
                ->whereIn('image', $fullRemovedPaths)
                ->whereNull('expires_at')
                ->update(['expires_at' => Carbon::now()]);
        }
        
        // Process new gallery images (UploadedFile objects)
        foreach ($galleryArray as $image) {
            // Check if it's a new file (UploadedFile instance)
            if (is_object($image) && method_exists($image, 'store')) {
                $path = $image->store('images', 'public');
                $savedImagePath = "/storage/$path"; // Full path for database
                
                Gallery::create([
                    'user_id' => $record->user_id,
                    'model_id' => $record->id,
                    'type' => 'products',
                    'image' => $savedImagePath,
                    'preview' => 0,
                    'placement' => 'gallery',
                    'size' => Collapse::bytesToMegabytes($image->getSize()),
                ]);
                
                OptimizeMedia::dispatch('public', $path);
                
                // Add saved path to gallery paths for form update (will be converted to Filament format later)
                $newGalleryPaths[] = $savedImagePath;
            } elseif (is_string($image)) {
                // Existing path - could be Filament format (images/...) or DB format (/storage/images/...)
                // Convert to DB format if needed
                if (!str_starts_with($image, '/storage/')) {
                    // Filament format - convert to DB format
                    $newGalleryPaths[] = "/storage/$image";
                } else {
                    // Already DB format
                    $newGalleryPaths[] = $image;
                }
            }
        }
        
        // After saving, reload record with fresh relationships
        $this->record = $this->record->fresh(['preview', 'gallery']);
        
        // Helper function to convert database path (/storage/images/...) to Filament format (images/...)
        $convertPathForFilament = function($path) {
            if (empty($path)) return null;
            // Remove /storage/ prefix and return relative path from disk root
            $path = str_replace('/storage/', '', $path);
            $path = ltrim($path, '/');
            return $path ?: null;
        };
        
        // Update form state with saved image paths to prevent "Waiting for size" issue
        // Use relative paths from disk root (images/...) as Filament FileUpload expects
        $currentState = $this->form->getState();
        
        // Update preview image path if new file was saved
        if ($newPreviewPath !== null && is_string($newPreviewPath)) {
            // New file was saved - convert to Filament format (images/...)
            $convertedPath = $convertPathForFilament($newPreviewPath);
            $currentState['preview_image'] = $convertedPath ? [$convertedPath] : [];
        } elseif ($newPreviewPath === null) {
            // Preview was removed
            $currentState['preview_image'] = [];
        }
        // If existing path (string), form already has it, no update needed
        
        // Update gallery images paths - convert to Filament format (images/...)
        if (!empty($newGalleryPaths)) {
            $convertedGalleryPaths = array_map($convertPathForFilament, $newGalleryPaths);
            $convertedGalleryPaths = array_filter($convertedGalleryPaths);
            $currentState['gallery_images'] = array_values(array_unique($convertedGalleryPaths));
        }
        
        // Fill form with updated image paths (relative paths from disk root: images/...)
        $this->form->fill($currentState);
        
        // Show success notification
        Notification::make()
            ->success()
            ->title('Product updated')
            ->body('The product and images have been saved successfully.')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    $ordersCount = $record->getOrdersCount();
                    if ($ordersCount > 0) {
                        Notification::make()
                            ->warning()
                            ->title('Product has orders')
                            ->body("This product has {$ordersCount} associated order(s). The product will be hidden (soft deleted) but data will be preserved.")
                            ->persistent()
                            ->send();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Product deleted')
                        ->body('The product has been hidden (soft deleted).')
                ),
        ];
    }
}
