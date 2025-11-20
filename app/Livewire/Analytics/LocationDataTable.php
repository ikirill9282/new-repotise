<?php

namespace App\Livewire\Analytics;

use App\Services\Analytics\TrafficEngagementService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LocationDataTable extends Component
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

        $locations = $this->trafficService->getLocationData($startDate, $endDate);

        // Применяем поиск
        if ($this->search) {
            $locations = array_filter($locations, function($item) {
                return stripos($item['location'] ?? '', $this->search) !== false;
            });
        }

        // Пагинация вручную
        $page = request()->get('page', 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($locations, $offset, $perPage);

        return view('livewire.analytics.location-data-table', [
            'locations' => collect($paginated),
            'total' => count($locations),
            'perPage' => $perPage,
        ]);
    }
}

