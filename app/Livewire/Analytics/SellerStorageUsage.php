<?php

namespace App\Livewire\Analytics;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class SellerStorageUsage extends Component
{
    use WithPagination;

    public $search = '';
    public $filterLevel = '';

    public function render()
    {
        // TODO: Реализовать расчет использования хранилища продавцами
        // Пока возвращаем пустые данные
        $sellers = collect([]);

        return view('livewire.analytics.seller-storage-usage', [
            'sellers' => $sellers,
        ]);
    }
}

