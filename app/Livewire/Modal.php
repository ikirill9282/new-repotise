<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;


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

    public function mount(): void
    {
      $this->currentUrl = url()->current() . '?' . http_build_query($_GET);
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

        Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember);
        Session::regenerate();
        $this->deactivate();
        
        return redirect($this->currentUrl);
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

        User::create([
          'email' => $this->email,
          'username' => $uname = preg_replace("/^(.*?)@.*#/is", "$1", $this->email),
          'name' => ucfirst($uname),
          'password' => $this->password,
        ]);

        $this->close();
        $this->openSuccess();

        return ;
      }

      $this->addErrorText('reg.email', 'This email address is already registered. Sign In instead?');
    }

    public function sendResetCode()
    {
      $this->open = true;
      $this->view = 'reset_confirm';
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

      // User::whereHas('reset_code', fn($query) => $query->where('code', $this->code))->update(['password' => $this->new_password]);

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

    public function render()
    {
        return view("livewire.modal");
    }
}


