<?php

namespace App\Livewire\Analytics;

use App\Services\Analytics\ContentPerformanceService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class TopPerformingContentTable extends Component
{
    use WithPagination;

    public $search = '';

    protected $contentService;

    public function mount(): void
    {
        $this->contentService = new ContentPerformanceService();
    }

    public function render()
    {
        $startDate = request()->get('start_date')
            ? Carbon::parse(request()->get('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = request()->get('end_date')
            ? Carbon::parse(request()->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $content = $this->contentService->getTopPerformingContent($startDate, $endDate, 50);

        // Применяем поиск
        if ($this->search) {
            $content = array_filter($content, function($item) {
                return stripos($item['title'] ?? '', $this->search) !== false
                    || stripos($item['author_name'] ?? '', $this->search) !== false;
            });
        }

        // Пагинация вручную
        $page = request()->get('page', 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($content, $offset, $perPage);

        return view('livewire.analytics.top-performing-content-table', [
            'content' => collect($paginated),
            'total' => count($content),
            'perPage' => $perPage,
        ]);
    }
}

