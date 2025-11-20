<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoryResource\Pages;
use App\Filament\Resources\HistoryResource\RelationManagers;
use App\Models\History;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class HistoryResource extends Resource
{
    protected static ?string $model = History::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

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
            ->query(static::getEloquentQuery()->orderByDesc('created_at'))
            ->recordUrl(fn() => null)
            ->columns([
                TextColumn::make('initiator')
                  ->formatStateUsing(function($state, $record) {
                    if ($state == 0) return 'System';
                    return $record->initer->username;
                  })
                  ,
                TextColumn::make('user.name')
                  ,
                TextColumn::make('action')
                  ,
                TextColumn::make('type')
                  ->badge()
                  ->color(fn($record) => match($record->type) {
                    'success' => Color::Emerald,
                    'error' => Color::Rose,
                    'warning' => Color::Amber,
                    'info' => Color::Sky,
                    'exception' => Color::Red,
                  })
                  ,
                TextColumn::make('message')
                  ,
                TextColumn::make('value')
                  ,
                TextColumn::make('old_value')
                  ,
                // TextColumn::make('payload')
                  // ,
                TextColumn::make('created_at')
                  ->icon('heroicon-o-clock')
                  ,
                // TextColumn::make('updated_at')
                //   ->icon('heroicon-o-clock')
                //   ,
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Action::make('view')
                //   ->link()
                //   ->icon('heroicon-o-eye')
                //   ->modal()
                //   ->modalContent(null)
                //   ,
            ], position: ActionsPosition::BeforeColumns)
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
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
            'index' => Pages\ListHistories::route('/'),
            // 'create' => Pages\CreateHistory::route('/create'),
            // 'edit' => Pages\EditHistory::route('/{record}/edit'),
        ];
    }
}
