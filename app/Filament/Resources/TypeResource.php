<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeResource\Pages;
use App\Filament\Resources\TypeResource\RelationManagers;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\ActionsPosition;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;

    protected static ?string $navigationGroup = 'Products';

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
          ->schema([
              TextInput::make('title'),
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
                  ->url(fn($record) => url("/admin/types/$record->id/edit"))
                  ->color(Color::Sky)
                  ,
                TextColumn::make('slug')
                  ->searchable()
                  ->sortable()
                  ,
                TextColumn::make('created_at')
                  ->searchable()
                  ->sortable()
                  ->toggleable()
                  ,
                TextColumn::make('updated_at')
                  ->searchable()
                  ->sortable()
                  ->toggleable()
                  ,
            ])
            ->filters([
                //
            ])
            ->actions([
                
              ActionGroup::make([
                EditAction::make(),
                ViewAction::make('view')
                  ->url(fn (Type $record): string => url('products/?type=' . $record->slug))
                  ->extraAttributes(['target' => '_blank'])
                  ,
                
                Action::make('Approve')
                  ->visible(fn (Type $record): bool => $record->status_id == 3)
                  ->action(function (Type $record) {
                      $record->update(['status_id' => 1]);
                  })
                  ,
                Action::make('Reject')
                  ->visible(fn (Type $record): bool => $record->status_id == 3)
                  ->action(function (Type $record) {
                      $record->update(['status_id' => 5]);
                  })
                  ,

                DeleteAction::make(),
              ]),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
          ;
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
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'edit' => Pages\EditType::route('/{record}/edit'),
        ];
    }
}
