<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (ValidationException $e) {
            // If ValidationException was thrown in the model, show the error
            if ($e->errors()) {
                Notification::make()
                    ->danger()
                    ->title('Validation error')
                    ->body($e->getMessage() ?: 'Location with this title already exists. Please choose a different name.')
                    ->send();
            }
            throw $e;
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database unique constraint violations
            if (str_contains($e->getMessage(), 'locations.locations_title_unique') || 
                str_contains($e->getMessage(), 'Duplicate entry')) {
                
                preg_match("/Duplicate entry '(.+?)' for key/", $e->getMessage(), $matches);
                $locationTitle = $matches[1] ?? 'unknown';
                
                Notification::make()
                    ->danger()
                    ->title('Location already exists')
                    ->body("Location with title '{$locationTitle}' already exists. Please select the existing location or choose a different name.")
                    ->send();
                
                throw ValidationException::withMessages([
                    'title' => "Location with title '{$locationTitle}' already exists. Please select the existing location or choose a different name.",
                ]);
            }
            
            throw $e;
        }
    }
}
