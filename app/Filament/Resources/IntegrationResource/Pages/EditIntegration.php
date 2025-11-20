<?php

namespace App\Filament\Resources\IntegrationResource\Pages;

use App\Filament\Resources\IntegrationResource;
use App\Models\History;
use App\Models\Integration;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditIntegration extends EditRecord
{
    protected static string $resource = IntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->disabled(fn() => $this->record->name === 'stripe'), // Prevent deleting Stripe
            Actions\ViewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure config is properly formatted
        if (isset($data['config']) && is_array($data['config'])) {
            // Remove empty values
            $data['config'] = array_filter($data['config'], fn($value) => $value !== null && $value !== '');
        }
        
        return $data;
    }

    protected function afterSave(): void
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
            $hasRequiredFields = !empty($config['property_id']);
        } else {
            $hasRequiredFields = !empty($config['api_key']);
        }
        
        // Update status if needed
        if ($hasRequiredFields && $integration->status === Integration::STATUS_NOT_CONFIGURED) {
            $integration->update(['status' => Integration::STATUS_INACTIVE]);
        } elseif (!$hasRequiredFields && $integration->status !== Integration::STATUS_NOT_CONFIGURED) {
            $integration->update(['status' => Integration::STATUS_NOT_CONFIGURED]);
        }
        
        // Log to history
        History::info()
            ->action('Integration Updated')
            ->initiator(Auth::id())
            ->message("Integration {$integration->name} configuration updated")
            ->payload(['ip_address' => request()->ip()])
            ->write();
    }
}

