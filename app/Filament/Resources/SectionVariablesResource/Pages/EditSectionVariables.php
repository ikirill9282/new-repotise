<?php

namespace App\Filament\Resources\SectionVariablesResource\Pages;

use App\Filament\Resources\SectionVariablesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSectionVariables extends EditRecord
{
    protected static string $resource = SectionVariablesResource::class;

    protected static ?string $title = 'Edit Section Variable';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
