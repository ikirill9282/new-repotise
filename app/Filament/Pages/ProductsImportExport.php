<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Filament\Resources\ProductResource;

class ProductsImportExport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static string $view = 'filament.pages.products-import-export';

    protected static ?string $navigationGroup = 'products';

    protected static ?string $navigationLabel = 'Import / Export';

    protected static ?int $navigationSort = 6;

    public function exportCsv()
    {
        return redirect(ProductResource::getUrl('index'))->with('export', 'csv');
    }

    public function exportExcel()
    {
        return redirect(ProductResource::getUrl('index'))->with('export', 'excel');
    }
}




