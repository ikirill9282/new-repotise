<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use App\Services\StripeWithdrawalProcessor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Support\Colors\Color;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\ActionsPosition;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationGroup = 'financials';

    protected static ?string $navigationLabel = 'Withdrawals';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['user']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Withdrawal Information')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
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
                        Withdrawal::STATUS_PENDING => 'Pending',
                        Withdrawal::STATUS_PROCESSING => 'Processing',
                        Withdrawal::STATUS_COMPLETED => 'Completed',
                        Withdrawal::STATUS_FAILED => 'Failed',
                        Withdrawal::STATUS_CANCELLED => 'Cancelled',
                    ])
                    ->required()
                    ->default(Withdrawal::STATUS_PENDING),

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
                    ->visible(fn ($record) => $record && $record->status === Withdrawal::STATUS_CANCELLED),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn () => null)
            ->columns([
                TextColumn::make('id')
                    ->label('Withdrawal ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User')
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
                    ->color(fn ($record) => match ($record->status) {
                        Withdrawal::STATUS_PENDING => 'warning',
                        Withdrawal::STATUS_PROCESSING => 'info',
                        Withdrawal::STATUS_COMPLETED => 'success',
                        Withdrawal::STATUS_FAILED => 'danger',
                        Withdrawal::STATUS_CANCELLED => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                TextColumn::make('stripe_payout_id')
                    ->label('Stripe Payout ID')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('processed_at')
                    ->label('Processed At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Withdrawal::STATUS_PENDING => 'Pending',
                        Withdrawal::STATUS_PROCESSING => 'Processing',
                        Withdrawal::STATUS_COMPLETED => 'Completed',
                        Withdrawal::STATUS_FAILED => 'Failed',
                        Withdrawal::STATUS_CANCELLED => 'Cancelled',
                    ]),
                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Withdrawal')
                        ->modalDescription('This will initiate the payout and mark the withdrawal as processing.')
                        ->visible(fn ($record) => $record->status === Withdrawal::STATUS_PENDING)
                        ->action(function (Withdrawal $record) {
                            try {
                                app(StripeWithdrawalProcessor::class)->process($record);
                                Notification::make()
                                    ->success()
                                    ->title('Withdrawal Approved')
                                    ->body('The withdrawal is now processing and the user has been notified by email.')
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Withdrawal Approval Failed')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Withdrawal')
                        ->modalDescription('Provide a reason. The funds will be refunded to the user balance.')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Rejection Reason')
                                ->required()
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->visible(fn ($record) => $record->status === Withdrawal::STATUS_PENDING)
                        ->action(function (Withdrawal $record, array $data) {
                            try {
                                app(StripeWithdrawalProcessor::class)->markRejectedAndRefund($record, (string) ($data['reason'] ?? 'Rejected'));
                                Notification::make()
                                    ->success()
                                    ->title('Withdrawal Rejected')
                                    ->body('Withdrawal rejected and funds refunded.')
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Withdrawal Reject Failed')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ])->position(ActionsPosition::BeforeColumns),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'create' => Pages\CreateWithdrawal::route('/create'),
            'view' => Pages\ViewWithdrawal::route('/{record}'),
            'edit' => Pages\EditWithdrawal::route('/{record}/edit'),
        ];
    }
}

