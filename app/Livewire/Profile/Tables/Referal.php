<?php

namespace App\Livewire\Profile\Tables;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Referal extends Component
{
    public function render()
    {
        $user = Auth::user();

        if (!$user) {
            return view('livewire.profile.tables.referal', [
                'user' => null,
                'summary' => [
                    'earnings' => 0.0,
                    'invited' => 0,
                ],
            ]);
        }

        $summary = [
            'earnings' => (float) $user->referal_income()->sum('sum'),
            'invited' => $user->referals()->count(),
        ];

        return view('livewire.profile.tables.referal', [
          'user' => $user,
          'summary' => $summary,
        ]);
    }
}
