<?php

namespace App\Livewire\Profile\Tables;

use App\Models\Review;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Reviews extends Component
{
    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.reviews', [
                'rows' => collect(),
                'summary' => [
                    'total_reviews' => 0,
                    'average_rating' => 0.0,
                ],
                'hasMore' => false,
            ]);
        }

        $baseQuery = Review::query()
            ->whereNull('parent_id')
            ->whereHas('product', fn($query) => $query->where('user_id', $userId));

        $averageRatingValue = (clone $baseQuery)->avg('rating');

        $summary = [
            'total_reviews' => (clone $baseQuery)->count(),
            'average_rating' => $averageRatingValue
                ? round((float) $averageRatingValue, 2)
                : 0.0,
        ];

        $rows = (clone $baseQuery)
            ->with(['author', 'product'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $hasMore = $summary['total_reviews'] > $rows->count();

        return view('livewire.profile.tables.reviews', [
            'rows' => $rows,
            'summary' => $summary,
            'hasMore' => $hasMore,
        ]);
    }
}
