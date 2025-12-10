<?php

namespace App\Livewire\Profile\Tables;

use Livewire\Component;
use App\Enums\Status;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ProfileProduct extends Component
{

  public $all_checked = false;

  public int $status_id;

  public string $sorting = 'newest';

  protected $listeners = ['products-refresh' => '$refresh'];

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

  public function duplicate(string $productId)
  {
    try {
      $product = Product::find(Crypt::decrypt($productId));
      
      if (!$product || $product->user_id !== Auth::id()) {
        $this->dispatch('toastError', ['message' => 'Product not found or access denied.']);
        return;
      }

      $newProduct = $product->replicate(['status_id', 'published_at', 'slug']);
      $newProduct->status_id = Status::DRAFT;
      $newProduct->published_at = null;
      $newProduct->save();

      // Копируем связи
      if ($product->types->isNotEmpty()) {
        $newProduct->types()->sync($product->types->pluck('id'));
      }
      if ($product->locations->isNotEmpty()) {
        $newProduct->locations()->sync($product->locations->pluck('id'));
      }
      if ($product->categories->isNotEmpty()) {
        $newProduct->categories()->sync($product->categories->pluck('id'));
      }

      // Копируем галерею без оптимизации (файлы уже оптимизированы)
      $product->copyGallery($newProduct, 'products');

      // Копируем subprice если есть
      if ($product->subscription && $product->subprice) {
        $newProduct->subprice()->create([
          'month' => $product->subprice->month,
          'quarter' => $product->subprice->quarter,
          'year' => $product->subprice->year,
        ]);
      }

      $this->dispatch('toastSuccess', ['message' => 'Product duplicated successfully!']);
      
      // Перенаправляем на страницу редактирования дублированного товара
      return redirect($newProduct->makeEditUrl());
    } catch (\Exception $e) {
      Log::error('Error duplicating product', [
        'product_id' => $productId,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);
      $this->dispatch('toastError', ['message' => 'Failed to duplicate product. Please try again.']);
    }
  }

  public function render()
  {
    $satuses = [$this->status_id];
    
    return view('livewire.profile.tables.profile-product', [
      'products' => Auth::user()->products()
        ->withCount('favorite')
        ->whereIn('status_id', $satuses)
        ->where('status_id', '!=', Status::DELETED)
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
