<?php

namespace App\Filament\Resources\SearchQueriesResource\Pages;

use App\Filament\Resources\SearchQueriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSearchQueries extends EditRecord
{
    protected static string $resource = SearchQueriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
