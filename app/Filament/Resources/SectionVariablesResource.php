<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectionVariablesResource\Pages;
use App\Filament\Resources\SectionVariablesResource\RelationManagers;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Admin\SectionVariables;
use App\Models\Admin\Page;
use App\Models\Admin\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\RichEditor;

class SectionVariablesResource extends Resource
{
  protected static ?string $model = SectionVariables::class;

  protected static ?string $navigationGroup = 'Layouts';

  // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
  
  protected static array $selectionFileds = [
    'aticle' => ['title', 'id'],
    'user' => ['username', 'id'],
  ];

  public ?Model $record = null;

  public static function form(Form $form): Form
  {
    return $form
      ->schema(fn($record) => static::selectRecordFields($record))
      ->columns(1);
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
      'index' => Pages\ListSectionVariables::route('/'),
      'create' => Pages\CreateSectionVariables::route('/create'),
      'edit' => Pages\EditSectionVariables::route('/{record}/edit'),
    ];
  }

  public static function selectRecordFields(?Model $record = null)
  {
    if (is_null($record)) {
      return [
        TextInput::make('value')
        ->required(),
        RichEditor::make('value')
          ->fileAttachmentsDisk('public')
          ->fileAttachmentsDirectory('images')
          ->fileAttachmentsVisibility('public')
      ];
    }

    if (str_contains($record->name, '_id')) {
      if (preg_match('/^.*_ids$/is', $record->name)) {
        $modelClass = static::getModelClass($record);
        $selected = static::getModelSelected($record);
        return [
          Select::make('value')
            ->multiple()
            ->options(function () use($selected, $modelClass) {
              return $modelClass::query()
                ->select($selected)
                ->get()
                ->pluck($selected[0], $selected[1])
                ->toArray();
            })
            ->maxItems(3)
        ];
      }

      if (preg_match('/^.*?_id$/is', $record->name)) {
        $modelClass = static::getModelName($record);
        $selected = static::getModelSelected($record);

        return [
          Select::make('value')
            ->options(function () use($selected, $modelClass) {
              return $modelClass::query()
                ->select($selected)
                ->get()
                ->pluck($selected[0], $selected[1])
                ->toArray();
            })
        ];
      }
    }

    if (str_contains($record->name, 'heading')) return [
      Select::make('value')
        ->options([
          'h1' => 'Heading lvl 1',
          'h2' => 'Heading lvl 2',
          'h3' => 'Heading lvl 3',
          'h4' => 'Heading lvl 4',
          'h5' => 'Heading lvl 5',
          'h5' => 'Heading lvl 6',
        ])
        ->required()
    ];

    if (strlen($record->value) > 100) {
      return [
        RichEditor::make('value')
          ->fileAttachmentsDisk('public')
          ->fileAttachmentsDirectory('images')
          ->fileAttachmentsVisibility('public')
      ];
    }

    return [
      TextInput::make('value')
        ->required(),
    ];
  }


  protected static function getModelClass(Model $record): ?string 
  {
    $name = static::getModelName($record);
    if ($name) {
      $name = ucfirst($name);
      $classMap = [
        "\App\Models\\$name",
        "\App\Models\Admin\\$name",
      ];

      foreach($classMap as $class) {
        if (class_exists($class)) {
          return $class;
        }
      }
    }

    return null;
  }

  protected static function getModelSelected(Model $record): array
  {
    $name = static::getModelName($record);
    if ($name === 'author') $name = 'user';
    
    return static::$selectionFileds[$name] ?? ['title', 'id'];
  }

  protected static function getModelName(Model $record): ?string
  {
    $arr = explode('_', $record->name);
    return $arr[0] ?? null;
  }
}
