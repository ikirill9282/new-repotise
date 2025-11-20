<?php

namespace App\Livewire\Analytics;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UserActivityBreakdownTable extends Component
{
    public function render()
    {
        $startDate = request()->get('start_date')
            ? Carbon::parse(request()->get('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $endDate = request()->get('end_date')
            ? Carbon::parse(request()->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // TODO: Реализовать получение данных из GA4 и User Login Logs
        $breakdown = [
            [
                'role' => 'Buyer',
                'active_users' => 0,
                'avg_sessions_per_user' => 0,
                'avg_session_duration' => '0:00',
            ],
            [
                'role' => 'Seller',
                'active_users' => 0,
                'avg_sessions_per_user' => 0,
                'avg_session_duration' => '0:00',
            ],
        ];

        return view('livewire.analytics.user-activity-breakdown-table', [
            'breakdown' => collect($breakdown),
        ]);
    }
}

