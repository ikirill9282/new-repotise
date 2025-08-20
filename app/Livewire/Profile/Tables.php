<?php

namespace App\Livewire\Profile;

use Livewire\Component;

class Tables extends Component
{
    public $tables = [
      [
        'name' => 'sales',
        'title' => 'Sales Snapshot',
      ],
      [
        'name' => 'product',
        'title' => 'Product Performance',
      ],
      [
        'name' => 'insights',
        'title' => 'Content Insights',
      ],
      [
        'name' => 'donation',
        'title' => 'Donation Summary',
      ],
      [
        'name' => 'refunds',
        'title' => 'Refunds Summary',
      ],
      [
        'name' => 'reviews',
        'title' => 'Recent Reviews',
      ],
      [
        'name' => 'referal',
        'title' => 'Referral Program Summary',
      ],
    ];

    public $activeTable = 'referal';

    public function setActive(string $name)
    {
      $this->activeTable = $name;
    }

    public function render()
    {
        return view('livewire.profile.tables');
    }
}
