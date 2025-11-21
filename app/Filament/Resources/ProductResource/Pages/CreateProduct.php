<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Gallery;
use App\Helpers\Collapse;
use App\Jobs\OptimizeMedia;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Store images temporarily to process after creation
        $this->previewImage = $data['preview_image'] ?? null;
        $this->galleryImages = $data['gallery_images'] ?? [];
        
        // Remove images from data as they will be processed separately
        unset($data['preview_image'], $data['gallery_images']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        // Handle preview image
        if (!empty($this->previewImage)) {
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
        
        // Handle gallery images
        if (!empty($this->galleryImages) && is_array($this->galleryImages)) {
            foreach ($this->galleryImages as $image) {
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
