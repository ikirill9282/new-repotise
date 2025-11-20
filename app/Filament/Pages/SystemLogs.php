<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SystemLogs extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.system-logs';

    protected static ?string $navigationGroup = 'settings';

    protected static ?string $navigationLabel = 'System Logs';

    protected static ?int $navigationSort = 5;

    public function mount(): void
    {
        // Redirect to log viewer if available
        if (class_exists(\Opcodes\LogViewer\Facades\LogViewer::class)) {
            redirect()->route('log-viewer.index');
        }
    }
}




