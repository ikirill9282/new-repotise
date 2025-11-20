<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefundRequestResource\Pages;
use App\Models\RefundRequest;
use App\Services\StripeRefundProcessor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;

class RefundRequestResource extends Resource
{
    protected static ?string $model = RefundRequest::class;

    // protected static ?string $navigationIcon = 'heroicon-o-arrow-path'; // Icon removed - group has icon

    protected static ?string $navigationGroup = 'financials';

    protected static ?string $navigationLabel = 'Refund Requests';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Refund Information')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'id')
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        
                        Forms\Components\Select::make('buyer_id')
                            ->label('Buyer')
                            ->relationship('buyer', 'name')
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        
                        Forms\Components\Select::make('seller_id')
                            ->label('Seller')
                            ->relationship('seller', 'name')
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                RefundRequest::STATUS_PENDING => 'Pending',
                                RefundRequest::STATUS_APPROVED => 'Approved',
                                RefundRequest::STATUS_REJECTED => 'Rejected',
                                RefundRequest::STATUS_REFUNDED => 'Refunded',
                                RefundRequest::STATUS_FAILED => 'Failed',
                            ])
                            ->required()
                            ->default(RefundRequest::STATUS_PENDING),
                        
                        Forms\Components\TextInput::make('refund_amount')
                            ->label('Refund Amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason')
                            ->rows(3)
                            ->disabled(fn ($record) => $record !== null),
                        
                        Forms\Components\Textarea::make('resolution_note')
                            ->label('Resolution Note')
                            ->rows(3)
                            ->visible(fn ($record) => $record && in_array($record->status, [RefundRequest::STATUS_REJECTED, RefundRequest::STATUS_FAILED])),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn() => null)
            ->columns([
                TextColumn::make('id')
                    ->label('Refund ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => \App\Filament\Resources\OrderResource::getUrl('edit', ['record' => $record->order_id]))
                    ->color(Color::Sky),
                
                TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => \App\Filament\Resources\UserResource::getUrl('view', ['record' => $record->buyer_id]))
                    ->color(Color::Sky),
                
                TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => \App\Filament\Resources\UserResource::getUrl('view', ['record' => $record->seller_id]))
                    ->color(Color::Sky),
                
                TextColumn::make('refund_amount')
                    ->label('Amount')
                    ->money(config('cashier.currency', 'usd'))
                    ->sortable(),
                
                TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->reason),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => match($record->status) {
                        RefundRequest::STATUS_PENDING => 'warning',
                        RefundRequest::STATUS_APPROVED => 'info',
                        RefundRequest::STATUS_REJECTED => 'danger',
                        RefundRequest::STATUS_REFUNDED => 'success',
                        RefundRequest::STATUS_FAILED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                
                TextColumn::make('created_at')
                    ->label('Date Requested')
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
                        RefundRequest::STATUS_PENDING => 'Pending',
                        RefundRequest::STATUS_APPROVED => 'Approved',
                        RefundRequest::STATUS_REJECTED => 'Rejected',
                        RefundRequest::STATUS_REFUNDED => 'Refunded',
                        RefundRequest::STATUS_FAILED => 'Failed',
                    ]),
                
                SelectFilter::make('buyer_id')
                    ->label('Buyer')
                    ->relationship('buyer', 'name')
                    ->searchable(),
                
                SelectFilter::make('seller_id')
                    ->label('Seller')
                    ->relationship('seller', 'name')
                    ->searchable(),
                
                DateRangeFilter::make('created_at')
                    ->label('Date Requested')
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
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Action::make('approve')
                        ->label('Approve Refund')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Refund')
                        ->modalDescription('This will process the refund through Stripe.')
                        ->visible(fn ($record) => $record->status === RefundRequest::STATUS_PENDING)
                        ->action(function (RefundRequest $record) {
                            try {
                                $processor = app(StripeRefundProcessor::class);
                                $result = $processor->process($record);
                                
                                // Обновляем статус на refunded после успешной обработки
                                if ($result->status === RefundRequest::STATUS_APPROVED) {
                                    $result->update(['status' => RefundRequest::STATUS_REFUNDED]);
                                }
                                
                                Notification::make()
                                    ->success()
                                    ->title('Refund Approved')
                                    ->body('The refund has been processed successfully.')
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Refund Failed')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                    
                    Action::make('reject')
                        ->label('Reject Refund')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Refund')
                        ->modalDescription('Please provide a reason for rejecting this refund request.')
                        ->form([
                            Forms\Components\Textarea::make('resolution_note')
                                ->label('Rejection Reason')
                                ->required()
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->visible(fn ($record) => $record->status === RefundRequest::STATUS_PENDING)
                        ->action(function (RefundRequest $record, array $data) {
                            $record->update([
                                'status' => RefundRequest::STATUS_REJECTED,
                                'resolution_note' => $data['resolution_note'],
                                'resolved_at' => now(),
                            ]);
                            
                            Notification::make()
                                ->success()
                                ->title('Refund Rejected')
                                ->body('The refund request has been rejected.')
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
            'index' => Pages\ListRefundRequests::route('/'),
            'view' => Pages\ViewRefundRequest::route('/{record}'),
        ];
    }
}
