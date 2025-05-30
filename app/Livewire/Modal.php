<?php

namespace App\Livewire;

use Livewire\Attributes\Url;
use App\Events\MailVerify;
use App\Events\ResetFailed;
use Livewire\Component;
use Livewire\Attributes\On; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Mail\ConfirmRegitster;
use App\Models\History;
use Illuminate\Support\Facades\Log;

class Modal extends Component
{
    public $open = false;
    public $active = false;
    public $view = 'auth';
    public $email = null;
    public $remember = false;
    public $as_seller = false;

    public $password = null;
    public $repeat_password = null;

    public $code = null;
    public $new_password = null;
    public $repeat_new_password = null;

    public $backup = null;

    public $errors = [];

    public string $currentUrl;

    #[Url(as:'modal', except: '')]
    public string $currentModal = '';

    public function mount(): void
    {
      // dd('mount');
      $this->currentUrl = url()->current() . '?' . http_build_query($_GET);
      if (request()->has('modal')) {
        if (!empty(request()->get('modal'))) {
          $this->open = true;
          $this->view = request()->get('modal');
          $this->activate();
        } else {
          $this->currentModal = '';
        }
      }
    }
    
    #[On('activate')]
    public function activate()
    {
      $this->active = true;
    }
    
    #[On('deactivate')]
    public function deactivate()
    {
      $this->active = false;
    }

    #[On('modal.close')]
    public function close()
    {
      $this->open = false;
      $this->currentModal = '';
    }

    #[On('modal.openAuth')]
    public function openAuth()
    {
      $this->open = true;
      $this->view = 'auth';
    }


    #[On('modal.openReg')]
    public function openReg()
    {
      $this->open = true;
      $this->view = 'reg';
    }

    #[On('modal.openSuccess')]
    public function openSuccess()
    {
      $this->open = true;
      $this->view = 'success';
    }

    #[On('modal.openReset')]
    public function resetPassword()
    {
      $this->currentModal = 'reset';
      $this->open = true;
      $this->view = 'reset';
    }

    public function openResetConfirm()
    {
      $this->open = true;
      $this->view ='reset_confirm';
    }

    public function auth()
    {
      if (!empty($this->email) && !empty($this->password)) {
        $user = User::firstWhere('email', $this->email);
        if (!$user) {
          $this->addErrorText('auth', 'Invalid email or password. Please try again.');
          return false;
        }

        if (!$user->active) {
          $this->addErrorText('auth', 'Your account is temporarily locked. Please try again later or contact support.');
          return false;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
          Session::regenerate(true);
          $this->deactivate();
          
          return redirect($user->makeProfileUrl());
        }
      }

      $this->addErrorText('auth', 'Invalid email or password. Please try again.');
    }

    public function reg()
    {
      // dump($this);
      if (!User::where('email', $this->email)->exists()) {
        
        if (!$this->validatePassword($this->password)) {
          $this->addErrorText('reg.password', 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.');
          return ;
        }

        if ($this->password !== $this->repeat_password) {
          $this->addErrorText('reg.password', 'Passwords do not match. Please re-enter.');
          $this->addErrorText('reg.repeat_password', 'Passwords do not match. Please re-enter.');
          return ;
        }

        $user = User::create([
          'email' => $this->email,
          'password' => $this->password,
        ]);
        $user->makeDefaultOptions();
        $user->sendVerificationCode(seller: $this->as_seller);
        
        $this->close();
        $this->openSuccess();

        return ;
      }

      $this->addErrorText('reg.email', 'This email address is already registered. Sign In instead?');
    }

    public function sendResetCode()
    {
      if (User::where('email', $this->email)->exists()) {
        $user = User::firstWhere('email', $this->email);
        $user->sendResetCode();
      }

      $this->open = true;
      $this->view = 'reset_confirm';
      $this->currentModal = 'reset_confirm';
    }

    public function openBackup()
    {
      $this->open = true;
      $this->view = 'backup';
    }

    public function useBackupCode()
    {
      $this->openSuccess();
    }

    public function confirmNewPassword()
    {
      if (!$this->validatePassword($this->new_password)) {
        $this->addErrorText('new_password', 'The password is too weak, it must be at least 8 characters long and include a combination of letters, numbers and symbols.');
        return ;
      }

      if ($this->new_password !== $this->repeat_new_password) {
        $this->addErrorText('new_password', 'Passwords do not match. Please re-enter.');
        $this->addErrorText('repeat_new_password', 'Passwords do not match. Please re-enter.');
        return ;
      }

      $user = User::whereHas('verify', fn($query) => $query->where([
        'code' => $this->code,
        'type' => 'reset',
      ]))
        ->with('verify')
        ->first();


      if (!$user) {
        $this->addErrorText('code', 'Code is expired');
        ResetFailed::dispatch(null, $this->code, 'code');
        return ;
      }

      if ($user->verify->where('type', 'reset')->first()?->code !== $this->code) {
        $this->addErrorText('code', 'Invalid code');
        ResetFailed::dispatch($user, $this->code, 'invalid');
      }

      $user->update(['password' => $this->new_password]);
      $user->verify()->where('type', 'reset')->delete();
  
      $this->openSuccess();
    }

    protected function addErrorText($key, $val)
    {
      $this->errors[$key] = $val;
    }

    protected function validatePassword(string $password)
    {
      return preg_match( '/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d!@#$%^&*()_\-+=]{8,}$/is', $password);
    }

    public function findUser(): ?User
    {
      return !empty($this->email) ? User::firstWhere('email', $this->email) : null;
    }

    public function render()
    {
        return view("livewire.modal");
    }
}


