<?php

namespace App\Livewire\Analytics;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SellerOnboardingFunnel extends Component
{
    public function render()
    {
        // Registered Sellers
        $registeredSellers = User::whereHas('roles', function($query) {
            $query->where('name', 'creator');
        })->count();

        // Stripe Connected (имеют stripe_id через Cashier)
        $stripeConnected = User::whereHas('roles', function($query) {
            $query->where('name', 'creator');
        })->whereNotNull('stripe_id')->count();

        // Basic Info Submitted (имеют заполненный профиль)
        $basicInfoSubmitted = User::whereHas('roles', function($query) {
            $query->where('name', 'creator');
        })->whereHas('options', function($query) {
            $query->whereNotNull('description');
        })->count();

        // Stripe Pending (через Stripe API)
        $stripePending = 0; // TODO: Получить через Stripe API

        // Stripe Verified
        $stripeVerified = User::whereHas('roles', function($query) {
            $query->where('name', 'creator');
        })->whereNotNull('stripe_verified_at')->count();

        $funnel = [
            ['step' => 'Registered Sellers', 'count' => $registeredSellers, 'percentage' => 100],
            ['step' => 'Stripe Connected', 'count' => $stripeConnected, 'percentage' => $registeredSellers > 0 ? round(($stripeConnected / $registeredSellers) * 100, 2) : 0],
            ['step' => 'Basic Info Submitted', 'count' => $basicInfoSubmitted, 'percentage' => $registeredSellers > 0 ? round(($basicInfoSubmitted / $registeredSellers) * 100, 2) : 0],
            ['step' => 'Stripe Pending', 'count' => $stripePending, 'percentage' => $registeredSellers > 0 ? round(($stripePending / $registeredSellers) * 100, 2) : 0],
            ['step' => 'Stripe Verified', 'count' => $stripeVerified, 'percentage' => $registeredSellers > 0 ? round(($stripeVerified / $registeredSellers) * 100, 2) : 0],
        ];

        return view('livewire.analytics.seller-onboarding-funnel', [
            'funnel' => collect($funnel),
        ]);
    }
}

