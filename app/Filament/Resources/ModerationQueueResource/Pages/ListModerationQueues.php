<?php

namespace App\Filament\Resources\ModerationQueueResource\Pages;

use App\Filament\Resources\ModerationQueueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModerationQueues extends ListRecords
{
    protected static string $resource = ModerationQueueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}

