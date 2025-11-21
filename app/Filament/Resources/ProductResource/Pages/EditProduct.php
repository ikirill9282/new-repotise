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
        
        // Load existing preview image - FileUpload expects array with path
        // For existing files, we pass the path as stored in database
        if ($this->record->preview && $this->record->preview->image) {
            // FileUpload stores paths relative to disk root or with /storage/ prefix
            // Use the path as stored in database
            $previewPath = $this->record->preview->image;
            // Convert to relative path (without /storage/) for Filament
            $previewPath = str_replace('/storage/', '', $previewPath);
            $data['preview_image'] = $previewPath ? [$previewPath] : [];
        } else {
            $data['preview_image'] = [];
        }
        
        // Load existing gallery images - FileUpload expects array of paths
        $galleryImages = $this->record->gallery()
            ->where('preview', 0)
            ->where('placement', 'gallery')
            ->whereNull('expires_at')
            ->get()
            ->map(function($item) {
                // Convert to relative path (without /storage/) for Filament
                return str_replace('/storage/', '', $item->image);
            })
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
            : null;
        
        $galleryImagesData = $data['gallery_images'] ?? [];
        $this->galleryImages = is_array($galleryImagesData) && !empty($galleryImagesData)
            ? $galleryImagesData
            : [];
        
        // Remove images from data as they will be processed separately
        unset($data['preview_image'], $data['gallery_images']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record->fresh(['preview', 'gallery']);
        
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
                
                Gallery::create([
                    'user_id' => $record->user_id,
                    'model_id' => $record->id,
                    'type' => 'products',
                    'image' => "/storage/$path",
                    'preview' => 1,
                    'placement' => 'site',
                    'size' => Collapse::bytesToMegabytes($newPreview->getSize()),
                ]);
                
                OptimizeMedia::dispatch('public', $path);
            }
            // If it's a string (existing path), do nothing (image unchanged)
        }
        
        // Handle gallery images
        $galleryArray = is_array($this->galleryImages) ? $this->galleryImages : [];
        
        // Normalize current gallery paths (existing images as strings)
        $currentGalleryPaths = array_filter(array_map(function($img) use ($normalizePath) {
            if (is_string($img)) {
                return $normalizePath($img);
            }
            return null; // New files (objects) will be processed separately
        }, $galleryArray));
        
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
                
                Gallery::create([
                    'user_id' => $record->user_id,
                    'model_id' => $record->id,
                    'type' => 'products',
                    'image' => "/storage/$path",
                    'preview' => 0,
                    'placement' => 'gallery',
                    'size' => Collapse::bytesToMegabytes($image->getSize()),
                ]);
                
                OptimizeMedia::dispatch('public', $path);
            }
            // If it's a string (existing path), do nothing (image unchanged)
        }
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
