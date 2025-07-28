<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create-news')
              ->label('Create News')
              ->outlined()
              ->icon('heroicon-o-plus')
              ->url(fn() => ArticleResource::getUrl('create', ['type' => 'news']))
              ,
            Actions\CreateAction::make()
              ->label('Create Article')
              ->outlined()
              ->icon('heroicon-o-plus')
              ,
        ];
    }
}
