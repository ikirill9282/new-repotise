<?php

namespace App\Livewire\Analytics;

use App\Services\Analytics\UserActivityService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class TopSellersTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $userService;

    public function mount(): void
    {
        $this->userService = new UserActivityService();
    }

    public function render()
    {
        $startDate = request()->get('start_date')
            ? Carbon::parse(request()->get('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = request()->get('end_date')
            ? Carbon::parse(request()->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $sellers = $this->userService->getTopSellers($startDate, $endDate, 100);

        // Применяем поиск
        if ($this->search) {
            $sellers = array_filter($sellers, function($item) {
                return stripos($item['name'] ?? '', $this->search) !== false
                    || stripos($item['username'] ?? '', $this->search) !== false;
            });
        }

        // Пагинация вручную
        $page = request()->get('page', 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($sellers, $offset, $perPage);

        return view('livewire.analytics.top-sellers-table', [
            'sellers' => collect($paginated),
            'total' => count($sellers),
            'perPage' => $perPage,
        ]);
    }
}

