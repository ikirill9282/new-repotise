<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SEOTools extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string $view = 'filament.pages.seo-tools';

    protected static ?string $navigationGroup = 'marketing';

    protected static ?string $navigationLabel = 'SEO Tools';

    protected static ?int $navigationSort = 4;
}




