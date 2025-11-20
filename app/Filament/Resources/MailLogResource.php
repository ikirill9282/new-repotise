<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailLogResource\Pages;
use App\Filament\Resources\MailLogResource\RelationManagers;
use App\Models\MailLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;

class MailLogResource extends Resource
{
    protected static ?string $model = MailLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';


    protected static ?string $navigationGroup = 'marketing';

    protected static ?string $navigationLabel = 'Email Logs';

    protected static ?int $navigationSort = 5;
    // protected static ?string $navigationLabel = 'Product List';

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
            ->recordUrl(fn() => null)
            ->query(static::getEloquentQuery()->orderByDesc('id'))
            ->columns([
                TextColumn::make('recipient')
                  ->searchable()
                  ->toggleable()
                  ->getStateUsing(function($record) {
                    if ($record->user()->exists()) {
                      return Blade::render('filament.tables.columns.author', ['model' => $record]);
                    }

                    return $record->recipient;
                  })
                  ->html()
                  ,
                TextColumn::make('status')
                  ->formatStateUsing(fn($state) => ucfirst($state))
                  ->badge()
                  ->color(fn($record) => match($record->status) {
                    'new' => Color::Gray,
                    'append' => Color::Amber,
                    'delivered' => Color::Emerald,
                    'failed' => Color::Rose,
                    'accepted' => Color::Blue,
                  })
                  ->searchable()
                  ->toggleable()
                  ,
                TextColumn::make('subject')
                  ->searchable()
                  ->toggleable()
                  ,
                TextColumn::make('trigger')
                  ->searchable()
                  ->toggleable()
                  ,
                TextColumn::make('message_id')
                    ->searchable()
                    ->toggleable()
                  ,
                TextColumn::make('mailgun_id')
                    ->searchable()
                    ->toggleable()
                  ,
                TextColumn::make('created_at')
                  ->icon('heroicon-o-clock')
                  ->searchable()
                  ->toggleable()
                  ,
                TextColumn::make('updated_at')
                  ->icon('heroicon-o-clock')
                  ->searchable()
                  ->toggleable()
                  ,
            ])
            ->filters([
                //
            ])
            // ->actions([
            //     Tables\Actions\EditAction::make(),
            // ])
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
            'index' => Pages\ListMailLogs::route('/'),
            // 'create' => Pages\CreateMailLog::route('/create'),
            // 'edit' => Pages\EditMailLog::route('/{record}/edit'),
        ];
    }
}
