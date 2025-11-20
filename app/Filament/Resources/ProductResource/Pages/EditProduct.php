<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

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
