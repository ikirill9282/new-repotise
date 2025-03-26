<?php

namespace App\Filament\Widgets;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Filament\Forms\Form;
use App\Models\Admin\Section;
use Carbon\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class PageSectionsWidget extends Widget implements HasForms, HasActions, HasTable
{
  use InteractsWithForms;
  use InteractsWithActions;
  use InteractsWithTable;

  protected array|string|int $columnSpan = 'full';

  protected static string $view = 'filament.widgets.page-sections-widget';

  public ?string $selected_section = null;

  public ?Model $record = null;

  protected function getForms(): array
  {
    return [
      'form',
      // 'variablesForm',
    ];
  }

  public function form(Form $form): Form
  {
    return $form->schema([
      Select::make('selected_section')
        ->options(
          Section::query()
            ->select(['id', 'title'])
            ->get()
            ->pluck('title', 'id')
            ->toArray()
        )
        ->reactive()
        ->afterStateUpdated(function ($state) {
          $this->dispatch('sectionFormUpdated', [
            'selected_section' => intval($state),
          ]);
        })
        ->suffixAction(
          FormAction::make('Add')
            ->icon('heroicon-m-clipboard-document-check')
            ->action(fn($state) => $this->createPageSectionRelation($state))
        ),
    ])
      ->columns(1)
      ;
  }

  public function table(Table $table): Table
  {
    // $query = Section::query()
    //   ->when($this->record, function($q) {
    //     $q->whereHas('pages', fn($subquery) => $subquery->where('pages.id', $this->record->id));
    //   })
    // ;

    
    $query = ($this->record) ? $this->record->sections()->getQuery() : Section::query();

    return $table->query($query)
      ->columns([
        TextColumn::make('title'),
        TextColumn::make('slug'),
        TextColumn::make('type'),
        TextColumn::make('component'),
        TextColumn::make('created_at'),
        TextColumn::make('updated_at'),
        TextColumn::make('order'),
      ])
      ->actions([
        EditAction::make('edit')
          ->url(fn (Section $record): string => route('filament.admin.resources.sections.edit', ['record' => $record->section_id]))
          ->openUrlInNewTab(),
      ])
    ;
  }

  protected function createPageSectionRelation(null|int|string $section_id)
  {
    if (is_null($section_id)) return;
    $section = Section::find($section_id)->copyWithVariables();
    $this->record->sections()->attach($section->id, ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
  }
}
