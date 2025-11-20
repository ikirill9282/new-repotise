<?php

namespace App\Filament\Resources\ContentErrorReportResource\Pages;

use App\Filament\Resources\ContentErrorReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContentErrorReports extends ListRecords
{
    protected static string $resource = ContentErrorReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Error reports are created by users, not admins
        ];
    }
}
