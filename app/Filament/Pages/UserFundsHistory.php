<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\UserFunds;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Colors\Color;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;

class UserFundsHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static string $view = 'filament.pages.user-funds-history';

    protected static ?string $navigationGroup = 'financials';

    protected static ?string $navigationLabel = 'User Funds / Balance History';

    protected static ?int $navigationSort = 7;

    public function table(Table|Infolist $table): Table|Infolist
    {
        if ($table instanceof Infolist) {
            return $table;
        }
        
        return $table
            ->query(UserFunds::query()->with('user'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => \App\Filament\Resources\UserResource::getUrl('view', ['record' => $record->user_id]))
                    ->color(Color::Sky),
                
                TextColumn::make('sum')
                    ->label('Amount')
                    ->money(config('cashier.currency', 'usd'))
                    ->sortable(),
                
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable(),
                
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
            ->defaultSort('created_at', 'desc');
    }
}


