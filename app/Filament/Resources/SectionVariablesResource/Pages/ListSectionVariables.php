<?php

namespace App\Filament\Resources\SectionVariablesResource\Pages;

use App\Filament\Resources\SectionVariablesResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\SectionFormWidget;
use App\Models\Admin\Section;
use App\Models\Admin\SectionVariables;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;
use Filament\Actions\CreateAction;

class ListSectionVariables extends ListRecords
{
  protected static string $resource = SectionVariablesResource::class;

  protected $listeners =  ['sectionFormUpdated'];

  public $selected_section = null;
  public $selected_page = null;

  public function sectionFormUpdated(array $args)
  {
    $this->selected_section = $args['selected_section'] ?? null;
    $this->selected_page = $args['selected_page'] ?? null;
    $this->resetTable();

  }
  public function table(Table $table): Table
  {
    $query = SectionVariables::query()
      ->when(
        $this->selected_page, 
        function($subquery) {
          $subquery->whereHas('section.pages', fn($q) => $q->where('pages.id', $this->selected_page));
        }
      )
      ->when(
        $this->selected_section,
        function($subquery) {
          $subquery->where('section_id', $this->selected_section);
      }
    );

    return $table
      ->query($query)
      ->columns([
        TextColumn::make('section.pages.title')->searchable(),
        TextColumn::make('section.title')->searchable(),
        TextColumn::make('name')->label('Variable name')->searchable(),
        TextColumn::make('value')->label('Variable value')->searchable()->extraAttributes(['class' => 'text-wrap']),
      ])
      ->filters([
        //
      ])
      ->actions([
        EditAction::make()
          ->form(fn($record) => $this->selectRecordFields($record)),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          // Tables\Actions\DeleteBulkAction::make(),
          BulkAction::make('edit')
            ->form([
              TextInput::make('value'),
            ])
            ->action(fn(Collection $records, BulkAction $action) => $this->bulkUpdate($records, $action)),
        ]),
      ]);
  }

  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make(),
    ];
  }

  public function getHeaderWidgets(): array
  {
    return [
      SectionFormWidget::make([
        'config' => [
          'details' => false,
        ]
      ]),
    ];
  }

  public function bulkUpdate(Collection $records, BulkAction $action): void
  {
     $records->map(fn($record) => $record->update($action->getFormData()));
  }
}
