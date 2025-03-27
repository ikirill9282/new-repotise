<?php

namespace App\Filament\Widgets;

use App\Models\Admin\Section;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class SectionWidget extends BaseWidget
{
    // protected array|string|int $columnSpan = 2;

    // protected static ?int $sort = 2;

    protected static ?string $heading = 'Sections';

    public ?Model $record = null;

    protected array $default_config = [];

    protected array $config = [];

    public function mount(array $config = [])
    {
      $this->config = array_merge($this->default_config, $this->config, $config);
    } 

    protected function getTableQuery(): Builder|Relation|null
    {
      return Section::query();
    }

    // public function table(Table $table): Table
    // {
    //     return $table
    //         ->query(Section::query())
    //         ->defaultPaginationPageOption(5)
    //         ->columns([
    //             TextColumn::make('title'),
    //             TextColumn::make('type'),
    //             TextColumn::make('slug'),
    //             TextColumn::make('component'),
    //             // TextColumn::make('created_at'),
    //             // TextColumn::make('updated_at'),
    //         ]);
    // }


    public function getColumnSpan(): int|string|array
    {
      return $this->config['cols'] ?? parent::getColumnSpan();
    }

}
