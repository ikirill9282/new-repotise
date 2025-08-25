<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Page extends Component
{
  public function render()
  {

    $user = Auth::user();
    $articles = $user->articles()
      ->with([
        'author',
        'tags',
        'likes' => function($query) {
          $query->with('author.options')->limit(3);
        }
      ])
      ->limit(4)
      ->get()
      ;

    return view('livewire.profile.page', [
      'user' => $user,
      'articles' => $articles,
    ]);
  }
}
