<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeResource\Pages;
use App\Models\Type;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Status;

class TypeResource extends Resource
{
    protected static ?string $model = Type::class;

    protected static ?string $navigationGroup = 'other';

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
            ->recordUrl(fn() => null)
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
                
                TextColumn::make('usages')
                  ->getStateUsing(function (Type $record) {
                      return $record->products()->count();
                  })
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
                
                SelectFilter::make('status_id')
                  ->label('Filter by Status')
                  ->options(Status::pluck('title', 'id'))
                ,
            ])
            ->actions([
                
              ActionGroup::make([
                EditAction::make(),
                ViewAction::make('view')
                  ->url(fn (Type $record): string => url('products/?type=' . $record->slug))
                  ->extraAttributes(['target' => '_blank'])
                  ,
                
                Action::make('Approve')
                  ->icon('heroicon-o-check-circle')
                  ->visible(fn (Type $record): bool => $record->status_id == 3)
                  ->action(function (Type $record) {
                      $record->update(['status_id' => 1]);
                  })
                  ,
                Action::make('Reject')
                  ->icon('heroicon-o-shield-exclamation')
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
