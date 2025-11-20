<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\CommentResource;

class CommunityComments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.community-comments';

    protected static ?string $navigationGroup = 'community';

    protected static ?string $navigationLabel = 'Comments';

    protected static ?int $navigationSort = 1;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return CommentResource::getUrl('index', $parameters, $isAbsolute, $panel);
    }
}

