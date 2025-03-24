<?php

namespace App\Filament\Widgets;

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

class SectionVariables extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
              SectionVariablesModel::query()
            )
            ->paginated()
            ->extremePaginationLinks()
            ->columns([
              TextColumn::make('section.pages.title')->searchable(),
              // TextColumn::make('section.title')->searchable(),
              TextColumn::make('name')->label('Variable name')->searchable(),
              TextColumn::make('value')->label('Variable value')->searchable(),
            ])
            ->defaultGroup(Group::make('section.title')->label("Section"))
            // ->defaultPaginationPageOption(10)
            
            ->filters([
              SelectFilter::make('page')
                ->query(function(Builder $query, $state) {

                })
                ->options(
                  Page::query()
                    ->select('id', 'title')
                    ->get()
                    ->pluck('title', 'id')
                    ->toArray()
                )
              ,
              SelectFilter::make('name')
                ->query(function(Builder $query, $state) {
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
            ])
            ->actions([
              EditAction::make()
                ->form(function(Model $record) {
                  if (str_contains($record->name, '_id')) {
                    if (preg_match('/^.*_ids$/is', $record->name)) {
                      return [
                        Select::make('value')
                          ->multiple()
                          ->options(function() {
                            return Article::query()
                              ->select(['id', 'title'])
                              ->get()
                              ->pluck('title', 'id')
                              ->toArray();
                          })
                          ->maxItems(3)
                      ];
                    }
                  }
                }),
            ])
            ->bulkActions([]);
    }
}
