<?php

namespace App\Filament\Resources\SectionVariablesResource\Pages;

use App\Filament\Resources\SectionVariablesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSectionVariables extends ListRecords
{
    protected static string $resource = SectionVariablesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
