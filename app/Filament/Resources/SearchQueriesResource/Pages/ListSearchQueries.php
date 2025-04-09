<?php

namespace App\Filament\Resources\SearchQueriesResource\Pages;

use App\Filament\Resources\SearchQueriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSearchQueries extends ListRecords
{
    protected static string $resource = SearchQueriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
