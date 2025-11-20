<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\History;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function afterCreate(): void
    {
        $page = $this->record;
        
        History::info()
            ->action('Page Created')
            ->initiator(Auth::id())
            ->message("Page '{$page->title}' was created")
            ->payload(['ip_address' => request()->ip()])
            ->write();
    }
}
