<?php

namespace App\Livewire\Modals;

use App\Helpers\CustomEncrypt;
use App\Models\Comment;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Report extends Component
{
  public $model;
  public $form = [
    'message' => null,
  ];

  public function mount($model, $resource)
  {
    $model_id = CustomEncrypt::getId($model);
    $query = match($resource) {
      'review' => Review::query(),
      'comment' => Comment::query(),
      default => null,
    };
    $this->model = $query->find($model_id);
  }

  public function submit()
  {
    $this->model->reports()->create([
      'user_id' => Auth::user()->id,
      'message' => $this->form['message'],
    ]);
    $this->dispatch('closeModal');
    $this->dispatch('toastSuccess', ['message' => 'Your report has been received and is awaiting review.']);
  }

  public function render()
  {
    return view('livewire.modals.report');
  }
}
