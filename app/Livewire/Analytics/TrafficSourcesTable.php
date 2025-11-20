<?php

namespace App\Livewire\Analytics;

use App\Services\Analytics\TrafficEngagementService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class TrafficSourcesTable extends Component
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

        $sources = $this->trafficService->getTrafficSources($startDate, $endDate);

        // Применяем поиск
        if ($this->search) {
            $sources = array_filter($sources, function($item) {
                return stripos($item['source'] ?? '', $this->search) !== false
                    || stripos($item['medium'] ?? '', $this->search) !== false;
            });
        }

        // Пагинация вручную
        $page = request()->get('page', 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($sources, $offset, $perPage);

        return view('livewire.analytics.traffic-sources-table', [
            'sources' => collect($paginated),
            'total' => count($sources),
            'perPage' => $perPage,
        ]);
    }
}
