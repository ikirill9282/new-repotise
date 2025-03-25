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

class SectionVariablesResource extends Resource
{
  protected static ?string $model = SectionVariables::class;

  protected static ?string $navigationGroup = 'Layouts';

  // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
  
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
      ->columns([
        TextColumn::make('section.pages.title')->searchable(),
        TextColumn::make('name')->label('Variable name')->searchable(),
        TextColumn::make('value')->label('Variable value'),
      ])
      ->filters([
        //
      ])
      ->defaultGroup(Group::make('section.title')->label("Section"))
      ->defaultPaginationPageOption(10)
      ->filters([
        SelectFilter::make('page')
          ->query(function (Builder $query, $state) {
            $page_id = filter_var($state['value'], FILTER_VALIDATE_INT) ? intval($state['value']) : null;
            $query->when(
              !is_null($page_id),
              function ($query) use ($page_id) {
                $query->whereHas('section.pages', function ($subquery) use ($page_id) {
                  $subquery->where('pages.id', $page_id);
                });
              }
            );
          })
          ->options(
            Page::query()
              ->select('id', 'title')
              ->get()
              ->pluck('title', 'id')
              ->toArray()
          ),
        SelectFilter::make('section')
          ->query(function($query, $state) {
            $section_id = filter_var($state['value'], FILTER_VALIDATE_INT) ? intval($state['value']) : null;
            $query->when(
              !is_null($section_id),
              fn($subquery) => $subquery->where('section_id', $section_id)
            );
          })
          ->options(
            Section::query()
              ->select(['id', 'title'])
              ->get()
              ->pluck('title', 'id')
              ->toArray()
          ),
        SelectFilter::make('name')
          ->query(function (Builder $query, $state) {
            $name = (filter_var($state['value'], FILTER_DEFAULT) && strlen($state['value'])) ? $state['value'] : null;
            $query->when(
              !is_null($name),
              fn($q) => $q->where('name', $name)
            );
          })
          ->options(
            function () {
              // dd($query->getFilters());
              $filters = [];
              // dump($filters);
              return SectionVariables::query()
                ->distinct()
                ->select(['name'])
                ->get()
                ->pluck('name', 'name');
            }
          )
          ->searchable()
      ])
      ->filtersTriggerAction(
        fn(TableAction $action) => $action
          ->button()
          ->label('Filter')
      )
      ->actions([
        EditAction::make()
          // ->form(fn($record) => $this->selectRecordField($record)),
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
      'index' => Pages\ListSectionVariables::route('/'),
      'create' => Pages\CreateSectionVariables::route('/create'),
      'edit' => Pages\EditSectionVariables::route('/{record}/edit'),
    ];
  }



  public function selectRecordField(Model $record)
  {
    if (str_contains($record->name, '_id')) {
      if (preg_match('/^.*_ids$/is', $record->name)) {
        $modelClass = $this->getModelName($record);
        $selected = $this->getModelSelected($record);
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
        $modelClass = $this->getModelName($record);
        $selected = $this->getModelSelected($record);

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

    return [
      TextInput::make('value')
        ->required(),
    ];
  }
}
