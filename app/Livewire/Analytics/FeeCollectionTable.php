<?php

namespace App\Livewire\Analytics;

use App\Models\Order;
use App\Models\OrderProducts;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class FeeCollectionTable extends Component
{
    use WithPagination;

    public $search = '';
    public $feeType = '';
    public $sellerId = '';

    public function render()
    {
        $startDate = request()->get('start_date') 
            ? Carbon::parse(request()->get('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = request()->get('end_date')
            ? Carbon::parse(request()->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $query = OrderProducts::query()
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->leftJoin('users', 'products.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status_id', '>=', 2)
            ->select(
                'order_products.*',
                'orders.id as order_id',
                'orders.created_at as order_date',
                'products.user_id as seller_id',
                'products.title as product_title',
                'users.name as seller_name',
                'users.username as seller_username'
            );

        if ($this->search) {
            $query->where(function($q) {
                $q->where('orders.id', 'like', "%{$this->search}%")
                  ->orWhere('products.title', 'like', "%{$this->search}%");
            });
        }

        if ($this->sellerId) {
            $query->where('products.user_id', $this->sellerId);
        }

        $fees = $query->orderByDesc('orders.created_at')
            ->paginate(25);

        return view('livewire.analytics.fee-collection-table', [
            'fees' => $fees,
        ]);
    }
}

