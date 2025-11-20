<?php

namespace App\Livewire\Analytics;

use App\Services\Analytics\TrafficEngagementService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LandingPagesTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $trafficService;

    public function mount(): void
    {
        $this->trafficService = new TrafficEngagementService();
    }

    public function render()
    {
        $startDate = request()->get('start_date')
            ? Carbon::parse(request()->get('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = request()->get('end_date')
            ? Carbon::parse(request()->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $pages = $this->trafficService->getLandingPages($startDate, $endDate, 100);

        // Применяем поиск
        if ($this->search) {
            $pages = array_filter($pages, function($item) {
                return stripos($item['url'] ?? '', $this->search) !== false;
            });
        }

        // Пагинация вручную
        $page = request()->get('page', 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($pages, $offset, $perPage);

        return view('livewire.analytics.landing-pages-table', [
            'pages' => collect($paginated),
            'total' => count($pages),
            'perPage' => $perPage,
        ]);
    }
}

