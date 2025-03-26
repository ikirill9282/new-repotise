<?php

namespace App\Filament\Widgets;

use App\Models\Admin\Page;
use Filament\Actions\Concerns\HasForm;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Forms\Components\Select;
use App\Models\Admin\Section;
use Illuminate\Database\Eloquent\Builder;

class SectionFormWidget extends BaseWidget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'admin.sections-form';

    // protected int | string | array $columnSpan = 2;

    public string|int|null $selected_section = null;
    public string|int|null $selected_page = null;

    protected array $default_config = [
      'cols' => 2,
      'details' => true,
    ];

    public array $config = [];

    public function mount(array $config = [])
    {
      $this->config = array_merge($this->default_config, $this->config, $config);
    }

    public function updated()
    {

      if(isset($this->oldFormState['selected_page']) && $this->selected_page != $this->oldFormState['selected_page']) {
        $this->selected_section = null;
      }
      $this->dispatch('sectionFormUpdated', [
        'selected_page' => $this->selected_page,
        'selected_section' => $this->selected_section,
      ]);
    }

    public function getColumnSpan(): int|string|array
    {
      return $this->config['cols'] ?? parent::getColumnSpan();
    }

    public function getSelectedSection()
    {
      return empty($this->selected_section) ? null : Section::find($this->selected_section);
    }

    protected function getStats(): array
    {
        return [
            //
        ];
    }

    public function form(Form $form): Form
    {
      return $form->schema([
        Select::make('selected_page')
          ->options(
            Page::query()
              ->select(['id', 'title'])
              ->get()
              ->pluck('title', 'id')
              ->toArray()
          )
          ->reactive()
          // ->afterStateUpdated(function($state) {

          // })
        ,
        Select::make('selected_section')
          ->options(
            Section::query()
              ->when($this->selected_page, function($query) {
                $query->whereHas('pages', fn($q) => $q->where('pages.id', $this->selected_page));
              })
              ->select(['id', 'title'])
              ->get()
              ->pluck('title', 'id')
              ->toArray()
          )
          ->reactive()
        ,
      ]);
    }


}
