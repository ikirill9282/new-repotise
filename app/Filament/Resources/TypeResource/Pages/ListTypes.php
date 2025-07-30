<?php

namespace App\Filament\Resources\TypeResource\Pages;

use App\Filament\Resources\TypeResource;
use App\Models\Type;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ListTypes extends ListRecords
{
    protected static string $resource = TypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
              ->modal()
              ->icon('heroicon-o-plus'),
            Action::make('merge')
              ->label('Merge Duplicates')
              ->modal()
              ->form([
                Select::make('primary')
                  ->label('Select Primary Taxonomy')
                  ->options(fn($get) => Type::whereNotIn('id', $get('secondary') ?? [])->pluck('title', 'id'))
                  ->required()
                  ,
                Select::make('secondary')
                  ->label('Select Taxonomies to Merge')
                  ->multiple()
                  ->options(fn($get) => Type::where('id', '!=', $get('primary'))->pluck('title', 'id'))
                  ->required()
                  ,
              ])
              ->action(function($action, $form) {
                $state = $form->getState();
                DB::transaction(function() use($state) {
                  Product::whereIn('type_id', $state['secondary'])->update(['type_id' => $state['primary']]);
                  Type::whereIn('id', $state['secondary'])->delete();
                });
              })
        ];
    }
}
