<?php

namespace App\Filament\Resources\PayoutResource\Pages;

use App\Filament\Resources\PayoutResource;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewPayout extends ViewRecord
{
    protected static string $resource = PayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolists\Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Payout Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Payout ID'),
                        
                        TextEntry::make('user.name')
                            ->label('Seller')
                            ->url(fn () => UserResource::getUrl('view', ['record' => $this->record->user_id])),
                        
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money(config('cashier.currency', 'usd')),
                        
                        TextEntry::make('currency')
                            ->label('Currency')
                            ->formatStateUsing(fn ($state) => strtoupper($state ?? config('cashier.currency', 'usd'))),
                        
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn () => match($this->record->status) {
                                \App\Models\Payout::STATUS_PENDING => 'warning',
                                \App\Models\Payout::STATUS_PROCESSING => 'info',
                                \App\Models\Payout::STATUS_COMPLETED => 'success',
                                \App\Models\Payout::STATUS_REJECTED => 'danger',
                                \App\Models\Payout::STATUS_FAILED => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        
                        TextEntry::make('stripe_payout_id')
                            ->label('Stripe Payout ID')
                            ->visible(fn () => !empty($this->record->stripe_payout_id)),
                        
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        
                        TextEntry::make('processed_at')
                            ->label('Processed At')
                            ->dateTime()
                            ->visible(fn () => !empty($this->record->processed_at)),
                    ])
                    ->columns(2),
                
                Section::make('Revenue Shares')
                    ->schema([
                        RepeatableEntry::make('revenueShares')
                            ->schema([
                                TextEntry::make('order_id')
                                    ->label('Order ID'),
                                TextEntry::make('product.title')
                                    ->label('Product'),
                                TextEntry::make('author_amount')
                                    ->label('Amount')
                                    ->money(config('cashier.currency', 'usd')),
                            ])
                            ->columns(3)
                            ->visible(fn () => $this->record->revenueShares->isNotEmpty()),
                    ])
                    ->visible(fn () => $this->record->revenueShares->isNotEmpty()),
                
                Section::make('Failure Information')
                    ->schema([
                        TextEntry::make('failure_message')
                            ->label('Failure Message')
                            ->visible(fn () => !empty($this->record->failure_message)),
                        
                        TextEntry::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->visible(fn () => !empty($this->record->rejection_reason)),
                    ])
                    ->visible(fn () => !empty($this->record->failure_message) || !empty($this->record->rejection_reason))
                    ->columns(1),
            ]);
    }
}
