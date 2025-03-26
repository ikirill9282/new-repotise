<?php

namespace App\Filament\Resources\SectionVariablesResource\Pages;

use App\Filament\Resources\SectionVariablesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\SectionFormWidget;
use App\Models\Admin\Section;
use App\Models\Admin\SectionVariables;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;

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
      // ->defaultGroup(Group::make('section.title')->label("Section"))
      // ->defaultPaginationPageOption(10)
      ->filters([
        // SelectFilter::make('page')
        //   ->query(function (Builder $query, $state) {
        //     $page_id = filter_var($state['value'], FILTER_VALIDATE_INT) ? intval($state['value']) : null;
        //     $query->when(
        //       !is_null($page_id),
        //       function ($query) use ($page_id) {
        //         $query->whereHas('section.pages', function ($subquery) use ($page_id) {
        //           $subquery->where('pages.id', $page_id);
        //         });
        //       }
        //     );
        //   })
        //   ->options(
        //     Page::query()
        //       ->select('id', 'title')
        //       ->get()
        //       ->pluck('title', 'id')
        //       ->toArray()
        //   ),
        // SelectFilter::make('section')
        //   ->query(function($query, $state) {
        //     $section_id = filter_var($state['value'], FILTER_VALIDATE_INT) ? intval($state['value']) : null;
        //     $query->when(
        //       !is_null($section_id),
        //       fn($subquery) => $subquery->where('section_id', $section_id)
        //     );
        //   })
        //   ->options(
        //     Section::query()
        //       ->select(['id', 'title'])
        //       ->get()
        //       ->pluck('title', 'id')
        //       ->toArray()
        //   ),
        // SelectFilter::make('name')
        //   ->query(function (Builder $query, $state) {
        //     $name = (filter_var($state['value'], FILTER_DEFAULT) && strlen($state['value'])) ? $state['value'] : null;
        //     $query->when(
        //       !is_null($name),
        //       fn($q) => $q->where('name', $name)
        //     );
        //   })
        //   ->options(
        //     SectionVariables::query()
        //       ->distinct()
        //       ->select(['name'])
        //       ->get()
        //       ->pluck('name', 'name')
        //   )
        //   ->searchable()
      ])
      // ->filtersTriggerAction(
      //   fn(TableAction $action) => $action
      //     ->button()
      //     ->label('Filter')
      // )
      ->actions([
        EditAction::make()
          ->form(fn($record) => $this->selectRecordFields($record)),
      ])
      ->bulkActions([
        BulkActionGroup::make([
          // Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
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
}
