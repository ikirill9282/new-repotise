<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\LoginHistory as LoginHistoryModel;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Colors\Color;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;

class LoginHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.login-history';

    protected static ?string $navigationGroup = 'settings';

    protected static ?string $navigationLabel = 'Login History';

    protected static ?int $navigationSort = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(LoginHistoryModel::query()->with('user'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->user ? \App\Filament\Resources\UserResource::getUrl('view', ['record' => $record->user_id]) : null)
                    ->color(Color::Sky),
                
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),
                
                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50),
                
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




