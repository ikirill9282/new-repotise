<?php

namespace App\Filament\Widgets;

use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use App\Models\Article;
use App\Models\Admin\SectionVariables as SectionVariablesModel;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Admin\Page;
use App\Models\Admin\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Filament\Actions\Action;
use App\Helpers\Slug;
use Filament\Forms\Concerns\HasFormComponentActions;
use Filament\Tables\Actions\DeleteAction;

class SectionVariablesWidget extends BaseWidget
{
  use HasFormComponentActions;

  public ?Model $record = null;

  // protected static ?int $sort = 1;

  protected int | string | array $columnSpan = 'full';

  protected static string $view = "filament.widgets.section-variables-widget";

  protected $listeners =  ['sectionFormUpdated'];

  public $selected_section = null;
  public $selected_page = null;

  public $name = null;
  public $value = null;

  protected array $searchableTableColumns = [
    'section.pages.title',
    'section.title',
    'name',
    'value',
  ];

  protected array $selectionFileds = [
    'aticle' => ['title', 'id'],
    'user' => ['username', 'id'],
  ];

  protected array $default_config = [
    'cols' => 'full',
    'search' => true,
    'filter' => true,
    'group' => true,
    'create' => true,
    'delete' => false,
  ];

  public array $config = [];

  public function mount(array $config = [])
  {
    $this->config = array_merge($this->default_config, $this->config, $config);
  }

  public function form(Form $form): Form
  {
    return $form->schema([
      TextInput::make('name'),
      RichEditor::make('value')
        ->fileAttachmentsDisk('public')
        ->fileAttachmentsDirectory('images')
        ->fileAttachmentsVisibility('public')
        ,
    ]);
  }

  public function createSectionVariableAction(): Action
  {
    return Action::make('createSectionVariable')
      ->action(fn($action) => $this->createSectionVariable());
  }

  public function sectionFormUpdated($args)
  {
    $this->selected_section = $args['selected_section'] ?? null;
    $this->selected_page = $args['selected_page'] ?? null;
    $this->resetTable();
  }

  public function table(Table $table): Table
  {

    // dump($this->selected_section, $this->selected_page);
    
    $query = SectionVariablesModel::query();
    if ($this->record && $this->record instanceof Section) {
      $query->where('section_id', $this->record->id);
      // return $this->getSectionPageTable($table, $query);
    }

    $query->when(
      $this->selected_page, 
      function($subquery) {
        $subquery->whereHas('section.pages', fn($q) => $q->where('pages.id', $this->selected_page));
      }
    );

    $query->when(
      $this->selected_section,
      function($subquery) {
        $subquery->where('section_id', $this->selected_section);
      }
    );

    // return $this->getMainWidgetTable($table, $query);
    $result = $table
      ->query($query)
      // ->paginated()
      // ->extremePaginationLinks()
      ->columns($this->buildColumns())
      ->defaultGroup($this->buildDefaultGroup())
      ->defaultPaginationPageOption(5)
      ->filters($this->buildFilters())
      ->actions($this->buildActions())
      ->bulkActions([])
      ;

    return $result;
  }

  public function buildColumns(): array
  {
    $cols = [
      TextColumn::make('section.pages.title'),
      TextColumn::make('section.title'),
      TextColumn::make('name')->label('Variable name'),
      TextColumn::make('value')->label('Variable value')->limit(200)->extraAttributes(['class' => 'text-wrap']),
    ];
    if ($this->config['search']) $cols = array_map(
      fn($col) => in_array($col->getName(), $this->searchableTableColumns)
        ? $col->searchable()
        : $col,
      $cols
    );

    if (isset($this->config['exclude']) && is_array($this->config['exclude'])) {
      $cols = array_filter(
        $cols,
        fn($col) => !in_array($col->getName(), $this->config['exclude']),
      );
    }

    return $cols;
  }


  public function buildDefaultGroup()
  {
    return $this->config['group']
      ? Group::make('section.title')->label("Section")
      : null;
  }

  public function buildActions()
  {
    $actions = [
      EditAction::make('edit')
          ->form(fn(Model $record) => $this->selectRecordFields($record))
          ->action(fn($record, $data) => $this->updateSectionVariable($record, $data))
          ,
    ];

    if ($this->config['delete']) {
      $actions[] = DeleteAction::make('delete')
        ->requiresConfirmation();
    }

    return $actions;
  }

  public function buildFilters()
  {
    if (!$this->config['filter']) return [];

    return [
      SelectFilter::make('page')
        ->query(function (Builder $query, $state) {})
        ->options(
          Page::query()
            ->select('id', 'title')
            ->get()
            ->pluck('title', 'id')
            ->toArray()
        ),
      SelectFilter::make('name')
        ->query(function (Builder $query, $state) {
          $filters = $this->tableFilters;
          $query->when(
            isset($filters['name']) && !empty($filters['name']['value']),
            fn($q) => $q->where('name', $filters['name']['value'])
          );
        })
        ->options(
          SectionVariablesModel::query()
            ->distinct()
            ->select(['name'])
            ->get()
            ->pluck('name', 'name')
        )
    ];
  }

  public function getColumnSpan(): int|string|array
  {
    return $this->config['cols'] ?? parent::getColumnSpan();
  }

  public function selectRecordFields(?Model $record = null)
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
        $modelClass = $this->getModelClass($record);
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
        $modelClass = $this->getModelClass($record);
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

  protected function getModelClass(Model $record): ?string 
  {
    $name = $this->getModelName($record);
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

  protected function getModelSelected(Model $record): array
  {
    $name = $this->getModelName($record);
    if ($name === 'author') $name = 'user';
    
    return $this->selectionFileds[$name] ?? ['title', 'id'];
  }

  protected function getModelName(Model $record): ?string
  {
    $arr = explode('_', $record->name);
    return $arr[0] ?? null;
  }

  protected function createSectionVariable()
  {
    SectionVariablesModel::create([
      'section_id' => $this->record->id,
      'name' => Slug::makeEn($this->name),
      'value' => $this->value,
    ]);
  }

  protected function updateSectionVariable(Model $record, array $data)
  {
    if (str_contains($data['value'], 'figure')) {
      preg_match_all('/<figure.*?<\/figure>/i', $data['value'], $figure);
      if (isset($figure[0])) {
        $figure = $figure[0];
        foreach ($figure as $item) {
          preg_match('/img\s+src="(.*?)"/i', $item, $img_src);
          $img_src = $img_src[1] ?? null;
          if ($img_src) {
            $img_path = preg_replace("/^.*?(\/storage.*?)$/is", "$1", $img_src);
            $img_url = url($img_path);
            $img = "<img src='$img_url' alt='Article image' />";
            $data['value'] = str_ireplace($item, $img, $data['value']);
          }
        }
      }
    }

    try {
      $record->update($data);
      Notification::make()
        ->title('Saved successfully')
        ->success()
        ->send();
    } catch (\Exception $e) {
      Notification::make()
        ->title($e->getMessage())
        ->error()
        ->send();
    }
  }
}
