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
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Enums\ActionsPosition;


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
              TextColumn::make('title')
                ->searchable()
                ->sortable()
                ->toggleable()
                ,
              TextColumn::make('slug')
                ->searchable()
                ->sortable()
                ->toggleable()
                ,
              TextColumn::make('status.title')
                ->searchable()
                ->sortable()
                ->badge()
                ->color(fn($record) => match($record->status_id) {
                  1 => Color::Emerald,
                  2 => Color::Indigo,
                  3 => Color::Amber,
                  4 => Color::Sky,
                  5 => Color::Red,
                  6 => Color::Orange,
                })
                ,
              TextColumn::make('created_at')
                ->searchable()
                ->sortable()
                ,
              TextColumn::make('updated_at')
                ->searchable()
                ->sortable()
                ,
            ])
            ->filters([
                //
            ])
            ->actions([
                
              ActionGroup::make([
                EditAction::make(),
                ViewAction::make('view')
                  ->url(fn (Location $record): string => url('products/' . $record->slug))
                  ->extraAttributes(['target' => '_blank'])
                  ,
                
                Action::make('Approve')
                  ->icon('heroicon-o-check-circle')
                  ->visible(fn (Location $record): bool => $record->status_id == 3)
                  ->action(function (Location $record) {
                      $record->update(['status_id' => 1]);
                  })
                  ,
                Action::make('Reject')
                  ->icon('heroicon-o-shield-exclamation')
                  ->visible(fn (Location $record): bool => $record->status_id == 3)
                  ->action(function (Location $record) {
                      $record->update(['status_id' => 5]);
                  })
                  ,

                DeleteAction::make(),
              ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
