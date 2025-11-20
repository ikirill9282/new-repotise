<?php

namespace App\Filament\Resources\ProductComplaintResource\Pages;

use App\Filament\Resources\ProductComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Report;
use App\Models\Product;

class ViewProductComplaint extends ViewRecord
{
    protected static string $resource = ProductComplaintResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->record;
        
        return [
            Actions\Action::make('unpublish_product')
                ->label('Unpublish Product')
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Unpublish Product')
                ->modalDescription('This will change the product status to Draft (unpublished).')
                ->action(function () use ($record) {
                    if ($record->reportable) {
                        $record->reportable->update(['status_id' => 2]); // Draft
                    }
                })
                ->visible(fn () => $record->reportable && $record->reportable->status_id !== 2),
            
            Actions\Action::make('block_seller')
                ->label('Block Seller')
                ->icon('heroicon-o-shield-exclamation')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Block Seller')
                ->modalDescription('This will block the seller account.')
                ->action(function () use ($record) {
                    if ($record->reportable && $record->reportable->author) {
                        $record->reportable->author->update(['status' => 'blocked']);
                    }
                })
                ->visible(fn () => $record->reportable && $record->reportable->author && $record->reportable->author->status !== 'blocked'),
            
            Actions\Action::make('resolve')
                ->label('Mark as Resolved')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('resolution_note')
                        ->label('Resolution Note')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->action(function (array $data) use ($record) {
                    $record->resolve(
                        auth()->id(),
                        $data['resolution_note'] ?? null
                    );
                })
                ->visible(fn () => $record->status !== Report::STATUS_RESOLVED),
        ];
    }
}
