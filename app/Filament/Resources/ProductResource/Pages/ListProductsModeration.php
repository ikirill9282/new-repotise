<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\ListRecords;
use App\Enums\Status;

class ListProductsModeration extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Product Moderation';

    protected static ?string $navigationLabel = 'Moderation';

    protected static ?string $navigationGroup = 'products';

    protected static ?int $navigationSort = 2;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getTableQuery()
            ->where('status_id', Status::PENDING);
    }
}




