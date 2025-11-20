<?php

namespace App\Filament\Resources\UserComplaintResource\Pages;

use App\Filament\Resources\UserComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserComplaints extends ListRecords
{
    protected static string $resource = UserComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Complaints are created by users, not admins
        ];
    }
}
