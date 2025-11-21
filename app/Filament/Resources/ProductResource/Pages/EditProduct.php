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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load relationships for image display
        $this->record->load(['preview', 'gallery']);
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store images temporarily to process after save
        $this->previewImage = $data['preview_image'] ?? null;
        $this->galleryImages = $data['gallery_images'] ?? [];
        
        // Remove images from data as they will be processed separately
        unset($data['preview_image'], $data['gallery_images']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record->fresh(['preview', 'gallery']);
        
        // Handle preview image
        if (!empty($this->previewImage)) {
            // Check if it's a new file (UploadedFile instance) or existing path (string)
            if (is_object($this->previewImage) && method_exists($this->previewImage, 'store')) {
                // Mark old preview as expired
                if ($record->preview?->exists()) {
                    $record->preview->update(['preview' => 0, 'expires_at' => Carbon::now()]);
                }
                
                $path = $this->previewImage->store('images', 'public');
                
                Gallery::create([
                    'user_id' => $record->user_id,
                    'model_id' => $record->id,
                    'type' => 'products',
                    'image' => "/storage/$path",
                    'preview' => 1,
                    'placement' => 'site',
                    'size' => Collapse::bytesToMegabytes($this->previewImage->getSize()),
                ]);
                
                OptimizeMedia::dispatch('public', $path);
            }
        }
        
        // Handle gallery images
        if (!empty($this->galleryImages) && is_array($this->galleryImages)) {
            foreach ($this->galleryImages as $image) {
                // Check if it's a new file (UploadedFile instance) or existing path (string)
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
            }
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
