<?php

namespace App\Livewire\Analytics;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ReferralRevenueTable extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $startDate = request()->get('start_date')
            ? Carbon::parse(request()->get('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = request()->get('end_date')
            ? Carbon::parse(request()->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $query = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_id', '>=', 2)
            ->whereHas('discount', function($query) {
                $query->where('group', 'referal');
            })
            ->with(['user', 'discount', 'order_products.product']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('id', 'like', "%{$this->search}%")
                  ->orWhereHas('user', function($uq) {
                      $uq->where('email', 'like', "%{$this->search}%")
                         ->orWhere('username', 'like', "%{$this->search}%");
                  });
            });
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate(25);

        return view('livewire.analytics.referral-revenue-table', [
            'orders' => $orders,
        ]);
    }
}

