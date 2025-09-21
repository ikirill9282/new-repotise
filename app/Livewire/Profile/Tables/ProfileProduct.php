<?php

namespace App\Livewire\Profile\Tables;

use Livewire\Component;
use App\Enums\Status;
use Illuminate\Support\Facades\Auth;

class ProfileProduct extends Component
{

  public $all_checked = false;

  public int $status_id;

  public function mount($active)
  {
    $this->status_id = match($active) {
      'products-active' => Status::ACTIVE,
      'products-draft' => Status::DRAFT,
      'products-pending' => Status::PENDING,
    };
  }

  public function render()
  {
    $satuses = [$this->status_id];
    
    return view('livewire.profile.tables.profile-product', [
      'products' => Auth::user()->products()
        ->whereIn('status_id', $satuses)
        ->orderByDesc('id')
        ->get()
    ]);
  }
}
