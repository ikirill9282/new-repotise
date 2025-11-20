<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Order as EnumsOrder;

class TransactionResource extends Resource
{
    protected static ?string $model = Order::class;

    // protected static ?string $navigationIcon = 'heroicon-o-credit-card'; // Icon removed - group has icon

    protected static ?string $navigationGroup = 'financials';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $currency = config('cashier.currency', 'usd');
        
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Transaction ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->user_id]))
                    ->color(Color::Sky),
                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($record) => 'Purchase') // TODO: Determine type from paymentable_type
                    ->badge()
                    ->color('info'),
                TextColumn::make('cost')
                    ->label('Amount')
                    ->money($currency)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($record) => EnumsOrder::label($record->status_id))
                    ->badge()
                    ->color(fn ($record) => EnumsOrder::color($record->status_id)),
                TextColumn::make('payments.stripe_id')
                    ->label('Payment ID')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_id')
                    ->label('Status')
                    ->options(EnumsOrder::toArray()),
                Filter::make('amount')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount_from')
                            ->label('Amount From')
                            ->numeric(),
                        \Filament\Forms\Components\TextInput::make('amount_to')
                            ->label('Amount To')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('cost', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('cost', '<=', $amount),
                            );
                    }),
                DateRangeFilter::make('created_at')
                    ->label('Date Range')
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
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
