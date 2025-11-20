<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class EmailCampaigns extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static string $view = 'filament.pages.email-campaigns';

    protected static ?string $navigationGroup = 'marketing';

    protected static ?string $navigationLabel = 'Email Campaigns';

    protected static ?int $navigationSort = 2;
}




