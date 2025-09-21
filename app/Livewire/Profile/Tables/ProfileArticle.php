<?php

namespace App\Livewire\Profile\Tables;

use App\Enums\Status;
use App\Models\Article;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProfileArticle extends Component
{
  public int $status_id;

  public function mount($active)
  {
    $this->status_id = match($active) {
      'articles-published' => Status::ACTIVE,
      'articles-scheduled' => Status::SCHEDULED,
      'articles-draft' => Status::DRAFT,
    };
  }

  public function render()
  {
    $statuses = [$this->status_id];
    if ($this->status_id == Status::SCHEDULED) $statuses[] = Status::PENDING;

    return view('livewire.profile.tables.profile-article', [
      'articles' => Auth::user()->articles()
        ->whereIn('status_id', $statuses)
        ->orderByDesc('id')
        ->get()
    ]);
  }
}
