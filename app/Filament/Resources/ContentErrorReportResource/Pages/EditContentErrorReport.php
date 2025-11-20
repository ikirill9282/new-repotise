<?php

namespace App\Filament\Resources\ContentErrorReportResource\Pages;

use App\Filament\Resources\ContentErrorReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContentErrorReport extends EditRecord
{
    protected static string $resource = ContentErrorReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
