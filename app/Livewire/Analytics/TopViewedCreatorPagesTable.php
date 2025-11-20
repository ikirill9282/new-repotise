<?php

namespace App\Livewire\Analytics;

use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class TopViewedCreatorPagesTable extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        // TODO: Реализовать через GA4 API для просмотров страниц профилей продавцов
        $creators = collect([]);

        return view('livewire.analytics.top-viewed-creator-pages-table', [
            'creators' => $creators,
        ]);
    }
}

