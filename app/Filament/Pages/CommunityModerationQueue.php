<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\ModerationQueueResource;

class CommunityModerationQueue extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static string $view = 'filament.pages.community-moderation-queue';

    protected static ?string $navigationGroup = 'community';

    protected static ?string $navigationLabel = 'Moderation Queue';

    protected static ?int $navigationSort = 5;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return ModerationQueueResource::getUrl('index', $parameters, $isAbsolute, $panel);
    }
}

