<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionResource\Pages;
use App\Filament\Resources\SectionResource\RelationManagers;
use App\Models\Admin\Section;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Get;
use Filament\Forms\Components\Field;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Illuminate\Support\Facades\Vite;
use App\Models\Article;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Grouping\Group;

class SectionResource extends Resource
{
  protected static ?string $model = Section::class;

  // protected static ?string $navigationParentItem = 'Layout';

  protected static ?string $navigationGroup = 'Layouts';

  // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

  public static function form(Form $form): Form
  {
    FilamentAsset::register([
      Css::make('app.css', Vite::useHotFile('admin.hot')
        ->asset('resources/css/app.css', 'build'))
    ]);
    return $form
      ->schema([
        TextInput::make('title')->required(),
        TextInput::make('slug')->required(),
        Select::make('type')
          ->options([
            'site' => 'site',
            'wire' => 'wire',
          ])
          ->required(),
        TextInput::make('component'),
      ])
      ->extraAttributes(['class' => 'w-full'])
      ->columns(1);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('title'),
        TextColumn::make('type'),
        TextColumn::make('slug'),
        TextColumn::make('component'),
        TextColumn::make('created_at'),
        TextColumn::make('updated_at'),
      ])
      ->groups([
        Group::make('page.title')
            ->label('Page #'),
      ])
      // ->striped()
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          // Tables\Actions\DeleteBulkAction::make(),
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
      'index' => Pages\ListSections::route('/'),
      'create' => Pages\CreateSection::route('/create'),
      'edit' => Pages\EditSection::route('/{record}/edit'),
    ];
  }
}
