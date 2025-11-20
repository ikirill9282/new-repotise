<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisputeResource\Pages;
use App\Models\Dispute;
use App\Models\RefundRequest;
use App\Services\StripeRefundProcessor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Notifications\Notification;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DisputeResource extends Resource
{
    protected static ?string $model = Dispute::class;

    // protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation'; // Icon removed - group has icon

    protected static ?string $navigationGroup = 'financials';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Dispute ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => OrderResource::getUrl('edit', ['record' => $record->order_id]))
                    ->color(Color::Sky),
                TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->buyer_id]))
                    ->color(Color::Sky),
                TextColumn::make('seller.name')
                    ->label('Seller')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->seller_id]))
                    ->color(Color::Sky),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Dispute::STATUS_OPEN => 'warning',
                        Dispute::STATUS_IN_REVIEW => 'info',
                        Dispute::STATUS_RESOLVED_BUYER => 'success',
                        Dispute::STATUS_RESOLVED_SELLER => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Dispute::STATUS_OPEN => 'Open',
                        Dispute::STATUS_IN_REVIEW => 'In Review',
                        Dispute::STATUS_RESOLVED_BUYER => 'Resolved (Buyer)',
                        Dispute::STATUS_RESOLVED_SELLER => 'Resolved (Seller)',
                        default => ucfirst($state),
                    }),
                TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('resolved_at')
                    ->label('Date Resolved')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Dispute::STATUS_OPEN => 'Open',
                        Dispute::STATUS_IN_REVIEW => 'In Review',
                        Dispute::STATUS_RESOLVED_BUYER => 'Resolved (Buyer)',
                        Dispute::STATUS_RESOLVED_SELLER => 'Resolved (Seller)',
                    ]),
                DateRangeFilter::make('created_at')
                    ->label('Date Created')
                    ->query(function ($query, array $data) {
                        if (!empty($data['created_at'])) {
                            $arr = explode('-', $data['created_at']);
                            $arr = array_map(fn($val) => Carbon::createFromFormat('d/m/Y', trim($val))->format('Y-m-d'), $arr);
                            return $query->whereBetween('created_at', ["$arr[0] 00:00:00", "$arr[1] 23:59:59"]);
                        }
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('set_in_review')
                        ->label('Set In Review')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->visible(fn ($record) => $record->status === Dispute::STATUS_OPEN)
                        ->action(function (Dispute $record) {
                            $record->update(['status' => Dispute::STATUS_IN_REVIEW]);
                            
                            Notification::make()
                                ->success()
                                ->title('Dispute set to In Review')
                                ->send();
                        }),
                    Action::make('resolve_buyer')
                        ->label('Resolve in favor of Buyer')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Resolve in favor of Buyer')
                        ->modalDescription('This will resolve the dispute in favor of the buyer. A refund will be automatically created if applicable.')
                        ->visible(fn ($record) => in_array($record->status, [Dispute::STATUS_OPEN, Dispute::STATUS_IN_REVIEW]))
                        ->action(function (Dispute $record) {
                            $order = $record->order;
                            
                            // Create refund request if order exists
                            if ($order) {
                                try {
                                    // Find order product to refund
                                    $orderProduct = $order->order_products()->first();
                                    
                                    if ($orderProduct && !$orderProduct->refunded) {
                                        $refundRequest = RefundRequest::create([
                                            'order_id' => $order->id,
                                            'order_product_id' => $orderProduct->id,
                                            'buyer_id' => $record->buyer_id,
                                            'seller_id' => $record->seller_id,
                                            'status' => RefundRequest::STATUS_PENDING,
                                            'reason' => 'dispute_resolution',
                                            'details' => 'Automatic refund created from dispute resolution',
                                        ]);
                                        
                                        // Process refund
                                        $processor = app(StripeRefundProcessor::class);
                                        $processor->process($refundRequest);
                                        
                                        if ($refundRequest->status === RefundRequest::STATUS_APPROVED) {
                                            $refundRequest->update(['status' => RefundRequest::STATUS_REFUNDED]);
                                        }
                                    }
                                } catch (\Exception $e) {
                                    // Log error but continue with dispute resolution
                                    \Log::error('Failed to create refund from dispute', [
                                        'dispute_id' => $record->id,
                                        'error' => $e->getMessage(),
                                    ]);
                                }
                            }
                            
                            $record->update([
                                'status' => Dispute::STATUS_RESOLVED_BUYER,
                                'resolved_at' => now(),
                            ]);
                            
                            Notification::make()
                                ->success()
                                ->title('Dispute Resolved')
                                ->body('Dispute resolved in favor of buyer. Refund processed if applicable.')
                                ->send();
                        }),
                    Action::make('resolve_seller')
                        ->label('Resolve in favor of Seller')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Resolve in favor of Seller')
                        ->modalDescription('This will resolve the dispute in favor of the seller. No refund will be issued.')
                        ->visible(fn ($record) => in_array($record->status, [Dispute::STATUS_OPEN, Dispute::STATUS_IN_REVIEW]))
                        ->action(function (Dispute $record) {
                            $record->update([
                                'status' => Dispute::STATUS_RESOLVED_SELLER,
                                'resolved_at' => now(),
                            ]);
                            
                            Notification::make()
                                ->success()
                                ->title('Dispute Resolved')
                                ->body('Dispute resolved in favor of seller.')
                                ->send();
                        }),
                ]),
            ], position: ActionsPosition::BeforeColumns)
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisputes::route('/'),
            'view' => Pages\ViewDispute::route('/{record}'),
        ];
    }
}
