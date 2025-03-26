<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Widgets\PageSectionsWidget;
use App\Filament\Widgets\SectionVariablesWidget;
use App\Models\Admin\Section;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Illuminate\Support\Js;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected $listeners = ['sectionFormUpdated', 'createdPageSection'];

    protected bool $enable_variables = false;

    // public $sections = [];

    // public function createdPageSection(Section $section)
    // {
    //   $this->sections[] = $section;
    // }

    public function sectionFormUpdated($args)
    {
      if (isset($args['selected_section']) && !empty($args['selected_section'])) {
        $this->enable_variables = true;
      }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // public function mount(int | string $record): void
    // {
    //   parent::mount($record);
    // }
    

    protected function getCancelFormAction(): Action
    {
      // dd(static::getResource()::getUrl());
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->alpineClickHandler('(window.location.href = "' . static::getResource()::getUrl() . '")')
            ->color('gray');
    }

    public function getFooterWidgets(): array
    {
      $widgets = [
        PageSectionsWidget::class,
      ];

      // if ($this->enable_variables) {
      //   $widgets[] = SectionVariablesWidget::make([
      //     'config' => [
      //       'cols' => 'full',
      //       'search' => false,
      //       'filter' => false,
      //       'group' => false,
      //       'exclude' => [
      //         'section.pages.title',
      //         'section.title',
      //       ]
      //     ]
      //   ]);
      // }

      return $widgets;
    }
}
