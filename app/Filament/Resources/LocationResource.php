<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationGroup = 'Products';

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->maxLength(250),
                TextInput::make('slug')->disabled(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
              TextColumn::make('title'),
              TextColumn::make('slug'),
              TextColumn::make('created_at')->disabled(),
              TextColumn::make('updated_at')->disabled(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modal(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListLocations::route('/'),
            // 'create' => Pages\CreateLocation::route('/create'),
            // 'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
