<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Gallery;
use App\Models\Location;
use App\Helpers\Collapse;
use App\Jobs\OptimizeMedia;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate locations - ensure all exist
        if (!empty($data['locations']) && is_array($data['locations'])) {
            $locationIds = $data['locations'];
            
            // Get all existing locations
            $existingLocationIds = Location::whereIn('id', $locationIds)->pluck('id')->toArray();
            
            // Check if all provided IDs exist
            $missingIds = array_diff($locationIds, $existingLocationIds);
            if (!empty($missingIds)) {
                Notification::make()
                    ->danger()
                    ->title('Invalid locations')
                    ->body('Some selected locations do not exist. Please select valid locations.')
                    ->send();
                
                throw ValidationException::withMessages([
                    'locations' => 'Some selected locations do not exist. Please select valid locations from the list.',
                ]);
            }
        }
        
        // Store images temporarily to process after creation
        $this->previewImage = $data['preview_image'] ?? null;
        $this->galleryImages = $data['gallery_images'] ?? [];
        
        // Remove images from data as they will be processed separately
        unset($data['preview_image'], $data['gallery_images']);
        
        return $data;
    }
    
    protected function beforeCreate(): void
    {
        // Additional validation - catch any attempts to create locations
        $data = $this->form->getState();
        
        if (!empty($data['locations']) && is_array($data['locations'])) {
            // Check for any string values that might indicate an attempt to create new locations
            foreach ($data['locations'] as $location) {
                if (is_string($location) && !is_numeric($location)) {
                    // Someone tried to pass a location name instead of ID
                    $existing = Location::where('title', $location)
                        ->orWhere('slug', $location)
                        ->first();
                    
                    if ($existing) {
                        Notification::make()
                            ->warning()
                            ->title('Location already exists')
                            ->body("Location '{$location}' already exists in the system. Please select it from the list instead.")
                            ->send();
                        
                        throw ValidationException::withMessages([
                            'locations' => "Location '{$location}' already exists in the system. Please select it from the list instead of creating a new one.",
                        ]);
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('Cannot create new location')
                            ->body("Location '{$location}' does not exist. Please create it in the Locations section first, or select an existing location.")
                            ->send();
                        
                        throw ValidationException::withMessages([
                            'locations' => "Location '{$location}' does not exist. Please create it in the Locations section first, or select an existing location.",
                        ]);
                    }
                }
            }
            
            // Validate all IDs exist
            $locationIds = array_filter($data['locations'], 'is_numeric');
            if (!empty($locationIds)) {
                $existingIds = Location::whereIn('id', $locationIds)->pluck('id')->toArray();
                $missingIds = array_diff($locationIds, $existingIds);
                
                if (!empty($missingIds)) {
                    Notification::make()
                        ->danger()
                        ->title('Invalid locations')
                        ->body('Some selected locations do not exist. Please select valid locations.')
                        ->send();
                    
                    throw ValidationException::withMessages([
                        'locations' => 'Some selected locations do not exist. Please select valid locations from the list.',
                    ]);
                }
            }
        }
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (UniqueConstraintViolationException $e) {
            // Check if it's a location duplicate error
            if (str_contains($e->getMessage(), 'locations.locations_title_unique') || 
                str_contains($e->getMessage(), 'Duplicate entry')) {
                
                // Extract location title from error message
                preg_match("/Duplicate entry '(.+?)' for key/", $e->getMessage(), $matches);
                $locationTitle = $matches[1] ?? 'unknown';
                
                // Check if location exists
                $existingLocation = Location::where('title', $locationTitle)->first();
                
                if ($existingLocation) {
                    Notification::make()
                        ->danger()
                        ->title('Location already exists')
                        ->body("Location '{$locationTitle}' already exists in the system. Please select it from the list instead of creating a new one.")
                        ->send();
                    
                    throw ValidationException::withMessages([
                        'locations' => "Location '{$locationTitle}' already exists. Please select the existing location from the list instead of creating a new one.",
                    ]);
                }
            }
            
            throw $e;
        } catch (QueryException $e) {
            // Check if it's a location duplicate error
            if (str_contains($e->getMessage(), 'locations.locations_title_unique') || 
                str_contains($e->getMessage(), 'Duplicate entry')) {
                
                // Extract location title from error message
                preg_match("/Duplicate entry '(.+?)' for key/", $e->getMessage(), $matches);
                $locationTitle = $matches[1] ?? 'unknown';
                
                // Check if location exists
                $existingLocation = Location::where('title', $locationTitle)->first();
                
                if ($existingLocation) {
                    Notification::make()
                        ->danger()
                        ->title('Location already exists')
                        ->body("Location '{$locationTitle}' already exists in the system. Please select it from the list instead of creating a new one.")
                        ->send();
                    
                    throw ValidationException::withMessages([
                        'locations' => "Location '{$locationTitle}' already exists. Please select the existing location from the list instead of creating a new one.",
                    ]);
                }
            }
            
            throw $e;
        }
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
