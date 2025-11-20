<?php

namespace App\Filament\Resources\UserComplaintResource\Pages;

use App\Filament\Resources\UserComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserComplaint extends EditRecord
{
    protected static string $resource = UserComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
