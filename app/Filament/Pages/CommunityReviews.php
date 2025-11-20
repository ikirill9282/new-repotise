<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\ReviewResource;

class CommunityReviews extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static string $view = 'filament.pages.community-reviews';

    protected static ?string $navigationGroup = 'community';

    protected static ?string $navigationLabel = 'Reviews';

    protected static ?int $navigationSort = 2;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return ReviewResource::getUrl('index', $parameters, $isAbsolute, $panel);
    }
}

