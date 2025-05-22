<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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
use Filament\Support\Colors\Color;
use Filament\Tables\Enums\ActionsPosition;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationGroup = 'Articles';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
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
                  ->color(Color::Sky)
                  ->url(fn($record) => url("/admin/tags/$record->id/edit"))
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
                  ->url(fn (Tag $record): string => url('products/?type=' . $record->slug))
                  ->extraAttributes(['target' => '_blank'])
                  ,
                
                Action::make('Approve')
                  ->visible(fn (Tag $record): bool => $record->status_id == 3)
                  ->action(function (Tag $record) {
                      $record->update(['status_id' => 1]);
                  })
                  ,
                Action::make('Reject')
                  ->visible(fn (Tag $record): bool => $record->status_id == 3)
                  ->action(function (Tag $record) {
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
