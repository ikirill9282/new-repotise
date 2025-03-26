<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Widgets\SectionFormWidget;
use App\Filament\Widgets\SectionVariablesWidget;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Form;
use App\Filament\Widgets\SectionWidget;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    // public function getWidgetData(): array
    // {
    //   return [
    //     'selected_section' => $this->selected_section,
    //     'selected_page' => $this->selected_page,
    //   ];
    // }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // public function form(Form $form): Form
    // {
    //   return $form->schema([
    //     TextInput::make('test'),
    //     TextInput::make('test2'),
    //   ]);
    // }


    public static function getWidgets(): array
    {
      return [
        SectionWidget::class,
        SectionVariablesWidget::class,
      ];
    }

    protected function getFooterWidgets(): array
    {
      return [
        SectionFormWidget::make([
          'config' => [
            'cols' => 2,
          ]
        ]),
        SectionVariablesWidget::make([
          'config' => [
            'cols' => 5,
            'search' => false,
            'filter' => false,
            'group' => false,
            'create' => false,
          ]
        ]),
      ];
    }

    public function getFooterWidgetsColumns(): int
    {
      return 7;
    }
}
