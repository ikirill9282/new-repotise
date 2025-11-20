<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class EmailTriggers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static string $view = 'filament.pages.email-triggers';

    protected static ?string $navigationGroup = 'marketing';

    protected static ?string $navigationLabel = 'Email Triggers';

    protected static ?int $navigationSort = 1;
}




