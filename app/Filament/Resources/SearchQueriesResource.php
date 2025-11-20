<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SearchQueriesResource\Pages;
use App\Filament\Resources\SearchQueriesResource\RelationManagers;
use App\Models\SearchQueries;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SearchQueriesResource extends Resource
{
    protected static ?string $model = SearchQueries::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationGroup = 'other';

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
            ->query(static::getEloquentQuery()->orderByDesc('id'))
            ->columns([
              TextColumn::make('text'),
              TextColumn::make('found'),
              TextColumn::make('created_at'),
              TextColumn::make('updated_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
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
            'index' => Pages\ListSearchQueries::route('/'),
            'create' => Pages\CreateSearchQueries::route('/create'),
            'edit' => Pages\EditSearchQueries::route('/{record}/edit'),
        ];
    }
}
