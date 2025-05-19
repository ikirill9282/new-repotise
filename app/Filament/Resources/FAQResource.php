<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FAQResource\Pages;
use App\Filament\Resources\FAQResource\RelationManagers;
use App\Models\FAQ;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Grouping\Group;


class FAQResource extends Resource
{
    protected static ?string $navigationGroup = 'Content';

    protected static ?string $model = FAQ::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQ';

    protected static ?string $slug = 'faq';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('text')
                  ->label('Question text')
                ,
                TextInput::make('type')
                  ->default('question')
                  ->disabled()
                ,
                Select::make('group')
                  ->options(static::getGroupOptions())
                ,
                Fieldset::make('Answer')
                  ->relationship('answer')
                  ->schema([
                    TextInput::make('type')
                      ->default('answer')
                      ->disabled(),
                    RichEditor::make('text'),
                  ])
                  ->mutateRelationshipDataBeforeCreateUsing(function($state) {
                    return $state;
                  })
                  ->columns(1)
                ,  
            ])
            ->columns(1);
    }

    public static function getEloquentQuery(): Builder
    {
      return parent::getEloquentQuery()->where('type', 'question');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('text')->extraAttributes(['class' => 'text-wrap'])
                  ->label('')
                  ->description(fn($record) => $record->answer->text)
                  ->searchable()
            ])
            ->defaultGroup(Group::make('group')->label("Group"))
            ->filters([
                SelectFilter::make('group')
                  ->options(static::getGroupOptions()),
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
            'index' => Pages\ListFAQS::route('/'),
            'create' => Pages\CreateFAQ::route('/create'),
            'edit' => Pages\EditFAQ::route('/{record}/edit'),
        ];
    }

    protected static function getGroupOptions()
    {
      return FAQ::select('group')->distinct()->get()->pluck('group', 'group');
    }
}
