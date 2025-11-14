<?php

namespace App\Livewire\Profile\Tables;

use Livewire\Component;
use App\Enums\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileProduct extends Component
{

  public $all_checked = false;

  public int $status_id;

  public string $sorting = 'newest';

  public function mount($active, ?string $sorting = null)
  {
    $this->status_id = match($active) {
      'products-active' => Status::ACTIVE,
      'products-draft' => Status::DRAFT,
      'products-pending' => Status::PENDING,
    };

    if (!empty($sorting)) {
      $this->sorting = $sorting;
    }
  }

  public function render()
  {
    $satuses = [$this->status_id];
    
    return view('livewire.profile.tables.profile-product', [
      'products' => Auth::user()->products()
        ->withCount('favorite')
        ->whereIn('status_id', $satuses)
        ->when(
          $this->sorting === 'price_low',
          fn($query) => $query
            ->orderByRaw('(price - COALESCE(sale_price, 0)) ASC')
            ->orderByDesc('id')
        )
        ->when(
          $this->sorting === 'price_high',
          fn($query) => $query
            ->orderByRaw('(price - COALESCE(sale_price, 0)) DESC')
            ->orderByDesc('id')
        )
        ->when(
          $this->sorting === 'rating',
          fn($query) => $query
            ->orderByDesc('rating')
            ->orderByDesc('id')
        )
        ->when(
          $this->sorting === 'alphabetical',
          fn($query) => $query
            ->orderBy('title')
            ->orderByDesc('id')
        )
        ->when(
          $this->sorting === 'oldest',
          fn($query) => $query
            ->orderBy(DB::raw('COALESCE(published_at, created_at, updated_at)'))
            ->orderByDesc('id')
        )
        ->when(
          $this->sorting === 'newest',
          fn($query) => $query
            ->orderByDesc(DB::raw('COALESCE(published_at, created_at, updated_at)'))
            ->orderByDesc('id')
        )
        ->when(
          ! in_array($this->sorting, ['price_low', 'price_high', 'rating', 'alphabetical', 'oldest', 'newest'], true),
          fn($query) => $query
            ->orderByDesc(DB::raw('COALESCE(published_at, created_at, updated_at)'))
            ->orderByDesc('id')
        )
        ->get()
    ]);
  }
}
