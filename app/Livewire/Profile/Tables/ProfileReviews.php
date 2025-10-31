<?php

namespace App\Livewire\Profile\Tables;

use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileReviews extends Component
{
    public string $sorting = 'newest';

    public int $perPage = 10;

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    protected function applySorting(Builder $query): Builder
    {
        return match ($this->sorting) {
            'rating_high' => $query->orderByDesc('rating')->orderByDesc('created_at'),
            'rating_low' => $query->orderBy('rating')->orderByDesc('created_at'),
            'oldest' => $query->orderBy('created_at'),
            default => $query->orderByDesc('created_at'),
        };
    }

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('livewire.profile.tables.profile-reviews', [
                'reviews' => collect(),
                'hasMore' => false,
            ]);
        }

        $baseQuery = Review::query()
            ->whereNull('parent_id')
            ->whereHas('product', fn ($query) => $query->where('user_id', $userId))
            ->with([
                'author',
                'product.preview',
            ])
            ->withCount([
                'messages as seller_reply_count' => fn ($query) => $query->where('user_id', $userId),
            ]);

        $total = (clone $baseQuery)->count();

        $orderedQuery = $this->applySorting(clone $baseQuery);

        $reviews = $orderedQuery
            ->limit($this->perPage)
            ->get();

        $hasMore = $total > $reviews->count();

        return view('livewire.profile.tables.profile-reviews', [
            'reviews' => $reviews,
            'hasMore' => $hasMore,
        ]);
    }
}
