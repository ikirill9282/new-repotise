<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Colors\Color;
use Filament\Tables\Filters\SelectFilter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Support\Carbon;

class ContactForms extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static string $view = 'filament.pages.contact-forms';

    protected static ?string $navigationGroup = 'community';

    protected static ?string $navigationLabel = 'Contact Forms';

    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(Form::query())
            ->columns([
                TextColumn::make('id')
                    ->label('Request ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(50),
                
                TextColumn::make('message')
                    ->label('Message')
                    ->limit(50)
                    ->wrap(),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => match($record->status ?? 'new') {
                        'new' => 'warning',
                        'in_progress' => 'info',
                        'processed' => 'success',
                        default => 'gray',
                    }),
                
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In Progress',
                        'processed' => 'Processed',
                    ]),
                
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




