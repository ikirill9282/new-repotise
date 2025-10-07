<?php

namespace App\Livewire\Modals;

use App\Helpers\SessionExpire;
use App\Traits\HasForm;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class Backup extends Component
{
    use HasForm;

    public array $form = [
      'code' => null,
    ];

    public ?string $user_id;

    public bool $shouldRender = true;

    public function mount(?string $user_id = null)
    {
      $this->user_id = $user_id;

      if (!$this->user_id || !$this->getUser() || !$this->canAttempt()) {
        $this->shouldRender = false;
      }
    }

    public function attempt()
    {
      if (!$this->canAttempt()) {
        $this->dispatch('toastError', ['message' => 'The login attempts have been exhausted. Please try again in one hour.']);
        $this->dispatch('openModal', 'auth');
        return ;
      }

      $available_attempts = SessionExpire::get('backup') ?? 0;
      $validator = Validator::make($this->form, [
        'code' => 'required|string|min:6|max:6|regex:/^[a-zA-Z0-9]+$/',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $user = $this->getUser();
      $valid = $validator->validated();
      $backups = $user->backup->pluck('code')->values()->toArray();
      
      if (!in_array($valid['code'], $backups)) {
        $validator->errors()->add('code', 'Invalid backup code.');
        SessionExpire::set('backup', ($available_attempts+1), Carbon::now()->modify('+1 hour'));

        throw new ValidationException($validator);
      }

      $user->update(['2fa' => 0]);
      $user->backup()->where('code', $valid['code'])->delete();
      
      if ($user->backup()->get()->isEmpty()) {
        $user->resetBackup();
      }
      
      $this->dispatch('openModal', 'backup-accept');
    }

    public function getUser(): ?User
    {
      return User::find(Crypt::decrypt($this->user_id));
    }

    protected function canAttempt(): bool
    {
      return $this->getUser()->canBackup();
    }

    public function render()
    {
      return view('livewire.modals.backup');
    }
}
