<?php

namespace App\Filament\Pages;

use App\Models\Admin\Section;
use App\Models\Admin\Page as ModelPage;
use App\Models\Admin\SectionVariables;
use App\Models\Article;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Widgets\SectionVariables as SectionVariablesWidget;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use \App\Models\Admin\Page as PageModel;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;

class Layout extends Page implements HasTable, HasForms
{
  use InteractsWithTable, InteractsWithForms;

  protected static ?string $navigationGroup = 'Layouts';

  protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

  protected static string $view = 'filament.pages.layout';

  public $defaultAction = 'onboarding';

  public ?string $selected_page = null;

  public static function canAccess(): bool
  {
    return Auth::check();
  }

  protected function getHeaderActions(): array
  {
    return [
      // Action::make('save')
      //     ->url(route('variables.edit')),
      Action::make('edit')
        ->url(route('variables.edit')),
      Action::make('delete')
        ->requiresConfirmation()
        ->action(fn() => dd($this)),
    ];
  }

  public function onboardingAction(): Action
  {
    return Action::make('onboarding')
      ->modalHeading('Welcome')
      ->visible(fn(): bool => empty(Auth::user()));
  }

  public function table(Table $table): Table
  {
    return $table
      ->query(
        SectionVariables::query()
      )
      ->columns([
        TextColumn::make('section.pages.title')->searchable(),
        TextColumn::make('name')->label('Variable name')->searchable(),
        TextColumn::make('value')->label('Variable value'),
      ])
      ->defaultGroup(Group::make('section.title')->label("Section"))
      ->defaultPaginationPageOption(10)
      ->filters([
        SelectFilter::make('page')
          ->query(function (Builder $query, $state) {
            $filters = $this->tableFilters;
            $query->when(
              (isset($filters['page']) && !empty($filters['page']['value'])),
              function ($query) use ($filters) {
                $query->whereHas('section.pages', function ($subquery) use ($filters) {
                  $subquery->where('pages.id', $filters['page']['value']);
                });
              }
            );
          })
          ->options(
            PageModel::query()
              ->select('id', 'title')
              ->get()
              ->pluck('title', 'id')
              ->toArray()
          )
          ,
        SelectFilter::make('name')
          ->query(function (Builder $query, $state) {
            $filters = $this->tableFilters;
            $query->when(
              (isset($filters['name']) && !empty($filters['name']['value'])),
              fn($q) => $q->where('name', $filters['name']['value'])
            );
          })
          ->options(
            function () {
              $filters = $this->tableFilters;
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
          ->form(fn($record) => $this->selectRecordField($record)),
      ])
      ->bulkActions([]);
  }


  protected function getFormSchema(): array
  {
    return [
      Select::make('selected_page')
        ->placeholder('Page...')
        ->options(
          ModelPage::select('id', 'title')
            ->orderBy('id')
            ->get()
            ->pluck('title', 'id')
            ->toArray(),
        )
        ->reactive()
        ->afterStateUpdated(function ($component, $get) {
          $this->selected_page = $get('selected_page');
          $this->resetTable();
        })
    ];
  }

  protected function getFooterWidgets(): array
  {
    return [
      // SectionVariablesWidget::class
    ];
  }

  public function getHeaderWidgetsColumns(): int | array
  {
    return 1;
  }

  public function getFooterWidgetsColumns(): int|string|array
  {
    return 4;
  }

  public function selectRecordField(Model $record)
  {
    if (str_contains($record->name, '_id')) {
      if (preg_match('/^.*_ids$/is', $record->name)) {
        $arr = explode('_', $record->name);
        $arr[0] = ($arr[0] == 'author') ? 'user' : $arr[0];
        $modelClass = "\App\Models\\" . ucfirst($arr[0]);

        $select = [
          'user' => ['id', 'username'],
          'default' => ['id', 'title'],
        ];

        $selected = isset($select[$arr[0]]) ? $select[$arr[0]] : $select['default'];
        
        return [
          Select::make('value')
            ->multiple()
            ->options(function () use($selected) {
              return $modelClass::query()
                ->select($selected)
                ->get()
                ->pluck('title', 'id')
                ->toArray();
            })
            ->maxItems(3)
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
