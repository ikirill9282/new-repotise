<?php

namespace App\Filament\Resources\ProductComplaintResource\Pages;

use App\Filament\Resources\ProductComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductComplaints extends ListRecords
{
    protected static string $resource = ProductComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
