<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\History;
use App\Models\Page;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn() => $this->record->type !== Page::TYPE_SYSTEM)
                ->requiresConfirmation()
                ->modalHeading('Delete Page')
                ->modalDescription('Are you sure you want to delete this page? This action cannot be undone.')
                ->action(function () {
                    $title = $this->record->title;
                    $this->record->delete();
                    
                    History::warning()
                        ->action('Page Deleted')
                        ->initiator(Auth::id())
                        ->message("Page '{$title}' was deleted")
                        ->payload(['ip_address' => request()->ip()])
                        ->write();
                    
                    Notification::make()
                        ->title('Page deleted')
                        ->success()
                        ->send();
                    
                    return redirect(static::getResource()::getUrl('index'));
                }),
        ];
    }

    protected function afterSave(): void
    {
        $page = $this->record;
        
        History::info()
            ->action('Page Updated')
            ->initiator(Auth::id())
            ->message("Page '{$page->title}' was updated")
            ->payload(['ip_address' => request()->ip()])
            ->write();
    }
}
