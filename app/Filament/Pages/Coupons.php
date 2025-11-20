<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Coupons extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static string $view = 'filament.pages.coupons';

    protected static ?string $navigationGroup = 'marketing';

    protected static ?string $navigationLabel = 'Coupons';

    protected static ?int $navigationSort = 3;
}




