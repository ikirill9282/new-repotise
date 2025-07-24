<?php

namespace App\Livewire;

use App\Enums\Action;
use Livewire\Attributes\Url;
use App\Events\MailVerify;
use App\Events\ResetFailed;
use App\Helpers\CustomEncrypt;
use App\Helpers\SessionExpire;
use Livewire\Component;
use Livewire\Attributes\On; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use App\Mail\ConfirmRegitster;
use App\Models\Discount;
use App\Models\History;
use App\Models\UserBackup;
use App\Services\Cart;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\UserReferal;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\DB;

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

    public ?string $currentRouteName = null;

    public array $currentRouteParams = [];

    public ?string $currentUrl = null;

    #[Url(as:'modal', except: '')]
    public string $currentModal = '';

    public function mount(): void
    {
      // dd('mount');
      $this->currentUrl = url()->current() . '?' . http_build_query($_GET);
      $this->currentRouteName = request()->route()->getName();
      $this->currentRouteParams = request()->all();

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

    #[On('modal.openCart')]
    public function openCart()
    {
      $this->currentModal = 'cart';
      $this->open = true;
      $this->view = 'cart';
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
          
          return redirect('/');
        }
      }

      $this->addErrorText('auth', 'Invalid email or password. Please try again.');
    }

    public function reg()
    {
      if (!User::where('email', $this->email)->exists()) {
        
        if (!User::validatePassword($this->password)) {
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
        
        if ($this->currentRouteName == 'referal' && array_key_exists('token', $this->currentRouteParams)) {
          DB::transaction(function() use ($user) {
            $id = CustomEncrypt::getId($this->currentRouteParams['token']);
            $owner = User::find($id);
            UserReferal::firstOrCreate(['owner_id' => $owner->id, 'referal_id' => $user->id]);
          });
        }

        History::userCreated($user);
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
      if (empty($this->backup) || !UserBackup::where('code', $this->backup)->exists()) {
        $this->addError('backup', 'Invalid backup code');
        return ;
      }

      $model = UserBackup::where('code', $this->backup)->with('user')->first();
      $model->user->update(['2fa' => 0]);
      $model->delete();

      History::activateBackupCode($model->user, $this->backup);

      $this->openSuccess();
    }

    public function confirmNewPassword()
    {
      if (!User::validatePassword($this->new_password)) {
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

      $op = $user->password;
      $user->update(['password' => $this->new_password]);
      $user->refresh();
      $user->verify()->where('type', 'reset')->delete();

      History::success()
        ->action(Action::RESET_PASSWORD)
        ->userId($user->id)
        ->values($op, $user->password)
        ->message('User password changed')
        ->write()
        ;
      
      $this->openSuccess();
      $this->currentModal = '';
    }

    protected function addErrorText($key, $val)
    {
      $this->errors[$key] = $val;
    }

    public function findUser(): ?User
    {
      return !empty($this->email) ? User::firstWhere('email', $this->email) : null;
    }

    public function moveCheckout()
    {
      $cart = new Cart();
      if ($cart->hasProducts()) {
        $order = Order::preparing($cart);
        $order->user_id = Auth::user()?->id ?? 0;
        $order = $order->savePrepared();

        $cart->flushCart();
        Session::put('checkout', $order->id);

        return redirect()->route('checkout');
      }
      
      return ;
    }

    public function render()
    {
        return view("livewire.modal");
    }
}


