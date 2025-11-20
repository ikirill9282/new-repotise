<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoliciesResource\Pages;
use App\Models\Policies;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PoliciesResource extends Resource
{
    protected static ?string $navigationGroup = 'other';

    protected static ?string $model = Policies::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                  ->required()
                  ,
                TextInput::make('slug')
                  ->required()
                  ,
                RichEditor::make('content')
                  ->disableToolbarButtons([
                    'attachFiles',
                  ])
                  ->columnSpanFull()
                  ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('slug'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPolicies::route('/'),
            'create' => Pages\CreatePolicies::route('/create'),
            'edit' => Pages\EditPolicies::route('/{record}/edit'),
        ];
    }
}
