<?php

namespace App\Livewire\Analytics;

use App\Services\Analytics\ContentPerformanceService;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AuthorStatisticsTable extends Component
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

        $authors = $this->contentService->getAuthorStatistics($startDate, $endDate);

        // Применяем поиск
        if ($this->search) {
            $authors = array_filter($authors, function($item) {
                return stripos($item['author_name'] ?? '', $this->search) !== false
                    || stripos($item['author_username'] ?? '', $this->search) !== false;
            });
        }

        // Пагинация вручную
        $page = request()->get('page', 1);
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($authors, $offset, $perPage);

        return view('livewire.analytics.author-statistics-table', [
            'authors' => collect($paginated),
            'total' => count($authors),
            'perPage' => $perPage,
        ]);
    }
}

