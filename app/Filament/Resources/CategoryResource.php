<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Enums\ActionsPosition;


class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'Products';

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = 2;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                TextInput::make('slug')->disabled(),
                Select::make('parent_id')
                  ->label('Parent Category')
                  ->options(Category::select('id', 'title')->get()->pluck('title', 'id')->toArray()),
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
                  ->url(fn($record) => url("/admin/categories/$record->id/edit"))
                  ->color(Color::Sky)
                  ,
                TextColumn::make('slug')
                  ->searchable()
                  ->sortable()
                  ,
                TextColumn::make('parent.title')
                  ->searchable()
                  ->sortable()
                  ,
                TextColumn::make('usages')
                  ->getStateUsing(function (Category $record) {
                      return $record->products()->count();
                  })
                ,
                TextColumn::make('status.title')
                  ->label('Status')
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
                TextColumn::make('created_at')->sortable(),
                TextColumn::make('updated_at')->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
              ActionGroup::make([
                EditAction::make(),
                ViewAction::make('view')
                  ->url(fn (Category $record): string => url('products/?categories=' . $record->slug))
                  ->extraAttributes(['target' => '_blank'])
                  ,
                
                Action::make('Approve')
                  ->icon('heroicon-o-check-circle')
                  ->visible(fn (Category $record): bool => $record->status_id == 3)
                  ->action(function (Category $record) {
                      $record->update(['status_id' => 1]);
                  })
                  ,
                Action::make('Reject')
                  ->icon('heroicon-o-shield-exclamation')
                  ->visible(fn (Category $record): bool => $record->status_id == 3)
                  ->action(function (Category $record) {
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
            'index' => Pages\ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
