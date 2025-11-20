<?php

namespace App\Filament\Resources\IntegrationResource\Pages;

use App\Filament\Resources\IntegrationResource;
use App\Models\History;
use App\Models\Integration;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateIntegration extends CreateRecord
{
    protected static string $resource = IntegrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure config is properly formatted
        if (isset($data['config']) && is_array($data['config'])) {
            // Remove empty values
            $data['config'] = array_filter($data['config'], fn($value) => $value !== null && $value !== '');
        }
        
        // Check if integration with this name already exists
        if (Integration::where('name', $data['name'])->exists()) {
            Notification::make()
                ->title('Integration already exists')
                ->body("An integration with name '{$data['name']}' already exists. Please use a different name.")
                ->danger()
                ->send();
            
            $this->halt();
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $integration = $this->record;
        
        // Update last_updated_at
        $integration->touchLastUpdated();
        
        // Determine status based on configuration
        $config = $integration->config ?? [];
        $hasRequiredFields = false;
        
        if ($integration->name === 'stripe') {
            $hasRequiredFields = !empty($config['api_key']) && !empty($config['secret_key']);
        } elseif ($integration->name === 'mailgun') {
            $hasRequiredFields = !empty($config['domain']) && !empty($config['api_key']);
        } elseif ($integration->name === 'ga4') {
            $hasRequiredFields = !empty($config['property_id']) || !empty($config['measurement_id']);
        } else {
            $hasRequiredFields = !empty($config['api_key']);
        }
        
        // Set status based on configuration
        if ($hasRequiredFields) {
            $integration->update(['status' => Integration::STATUS_INACTIVE]);
        } else {
            $integration->update(['status' => Integration::STATUS_NOT_CONFIGURED]);
        }
        
        // Log to history
        History::info()
            ->action('Integration Created')
            ->initiator(Auth::id())
            ->message("Integration {$integration->name} was created")
            ->payload(['ip_address' => request()->ip()])
            ->write();
    }
}

