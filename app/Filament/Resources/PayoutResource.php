<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayoutResource\Pages;
use App\Models\Payout;
use App\Services\StripePayoutProcessor;
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

class PayoutResource extends Resource
{
    protected static ?string $model = Payout::class;

    // protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray'; // Icon removed - group has icon

    protected static ?string $navigationGroup = 'financials';

    protected static ?string $navigationLabel = 'Payouts';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payout Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Seller')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->disabled(fn ($record) => $record !== null),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                Payout::STATUS_PENDING => 'Pending',
                                Payout::STATUS_PROCESSING => 'Processing',
                                Payout::STATUS_COMPLETED => 'Completed',
                                Payout::STATUS_REJECTED => 'Rejected',
                                Payout::STATUS_FAILED => 'Failed',
                            ])
                            ->required()
                            ->default(Payout::STATUS_PENDING),
                        
                        Forms\Components\TextInput::make('stripe_payout_id')
                            ->label('Stripe Payout ID')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('failure_message')
                            ->label('Failure Message')
                            ->rows(3)
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->rows(3)
                            ->visible(fn ($record) => $record && $record->status === Payout::STATUS_REJECTED),
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
                    ->label('Payout ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('user.name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => \App\Filament\Resources\UserResource::getUrl('view', ['record' => $record->user_id]))
                    ->color(Color::Sky),
                
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money(config('cashier.currency', 'usd'))
                    ->sortable(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => match($record->status) {
                        Payout::STATUS_PENDING => 'warning',
                        Payout::STATUS_PROCESSING => 'info',
                        Payout::STATUS_COMPLETED => 'success',
                        Payout::STATUS_REJECTED => 'danger',
                        Payout::STATUS_FAILED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                
                TextColumn::make('processed_at')
                    ->label('Processed At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('stripe_payout_id')
                    ->label('Stripe Payout ID')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Payout::STATUS_PENDING => 'Pending',
                        Payout::STATUS_PROCESSING => 'Processing',
                        Payout::STATUS_COMPLETED => 'Completed',
                        Payout::STATUS_REJECTED => 'Rejected',
                        Payout::STATUS_FAILED => 'Failed',
                    ]),
                
                SelectFilter::make('user_id')
                    ->label('Seller')
                    ->relationship('user', 'name')
                    ->searchable(),
                
                DateRangeFilter::make('created_at')
                    ->label('Created Date')
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
                        ->label('Approve Payout')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Payout')
                        ->modalDescription('This will create a payout in Stripe and process the payment to the seller.')
                        ->visible(fn ($record) => $record->status === Payout::STATUS_PENDING)
                        ->action(function (Payout $record) {
                            try {
                                $processor = app(StripePayoutProcessor::class);
                                $processor->process($record);
                                
                                Notification::make()
                                    ->success()
                                    ->title('Payout Approved')
                                    ->body('The payout has been processed successfully.')
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Payout Failed')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                    
                    Action::make('reject')
                        ->label('Reject Payout')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Payout')
                        ->modalDescription('Please provide a reason for rejecting this payout.')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Rejection Reason')
                                ->required()
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->visible(fn ($record) => $record->status === Payout::STATUS_PENDING)
                        ->action(function (Payout $record, array $data) {
                            $record->update([
                                'status' => Payout::STATUS_REJECTED,
                                'rejection_reason' => $data['rejection_reason'],
                            ]);
                            
                            Notification::make()
                                ->success()
                                ->title('Payout Rejected')
                                ->body('The payout has been rejected.')
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
            'index' => Pages\ListPayouts::route('/'),
            'view' => Pages\ViewPayout::route('/{record}'),
        ];
    }
}
