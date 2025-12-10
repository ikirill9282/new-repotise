<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Services\Cart as CartService;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Modals extends Component
{
    public ?string $modal = null;    
    public $isVisible = false;
    public $inited = false;
    public $args = [];

    public $oneTime = [
      'report', 
      'file-description', 
      'social', 
      'contact', 
      'auth-second', 
      'backup',
      'edit-contacts',
      'cancelsub',
      'delete-subscription',
      'delete-subscription-accept',
      'delete-product',
      'delete-product-accept',
    ];

    // Защищенные модальные окна, которые не должны закрываться при обновлении компонентов
    protected $protectedModals = ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept'];
    
    protected $previousModal = null;
    protected $previousIsVisible = false;

    public function mount()
    {
      $this->modal = request()->get('modal', null);
      if ($this->modal) {
          $this->isVisible = true;
          $this->inited = true;
          $this->startShowAnimation();
      } else {
          $this->isVisible = false;
      }
      
      $this->previousModal = $this->modal;
      $this->previousIsVisible = $this->isVisible;
    }
    
    public function updated($propertyName)
    {
        // Обновляем предыдущее состояние только для незащищенных модальных окон
        // Не восстанавливаем защищенные модальные окна здесь, чтобы избежать циклов обновления
        if (!in_array($this->modal, $this->protectedModals)) {
            if ($propertyName === 'modal') {
                $this->previousModal = $this->modal;
            }
            if ($propertyName === 'isVisible') {
                $this->previousIsVisible = $this->isVisible;
            }
        }
    }

    #[On('openModal')]
    public function openModal($modalName, $args = [])
    {
      $this->args = $args;
      $this->modal = $modalName;
      $this->inited = true;
      $this->startShowAnimation();
      
      // Сохраняем состояние для защищенных модальных окон
      if (in_array($modalName, $this->protectedModals)) {
          $this->previousModal = $modalName;
          $this->previousIsVisible = true;
      }

      // Всегда отправляем событие modal-opened для модальных окон успеха
      // чтобы предотвратить их автоматическое закрытие
      if (!in_array($modalName, $this->oneTime) || in_array($modalName, ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept'])) {
        $this->dispatch('modal-opened', ['modal' => $modalName]);
      }
    }
    
    #[On('openModalAfterClose')]
    public function openModalAfterClose($modalName, $args = [])
    {
      // Этот метод будет вызван через JavaScript после закрытия предыдущего модального окна
      // Убеждаемся, что предыдущее модальное окно полностью закрыто
      if ($this->isVisible && $this->modal !== $modalName) {
        // Если предыдущее окно еще видимо, принудительно закрываем его
        $this->isVisible = false;
        $this->modal = null;
      }
      
      // Открываем новое модальное окно
      $this->openModal($modalName, $args);
    }

    // Support for old modal events
    #[On('modal.openReg')]
    public function openReg()
    {
      $this->openModal('register');
    }

    #[On('modal.openAuth')]
    public function openAuth()
    {
      $this->openModal('auth');
    }

    #[On('closeModal')]
    public function closeModal()
    {
        $modalToClose = $this->modal;
        
        // Если закрывается модальное окно delete-product-accept, отправляем событие для обновления списка продуктов
        if ($modalToClose === 'delete-product-accept') {
            // Отправляем событие ДО закрытия модального окна, чтобы оно точно было обработано
            $this->dispatch('products-refresh');
        }
        
        // Сбрасываем защиту при закрытии модального окна пользователем
        if (in_array($modalToClose, $this->protectedModals)) {
            $this->previousModal = null;
            $this->previousIsVisible = false;
        }
        
        $this->isVisible = false;
        $this->dispatch('modal-closing-clean-url');
        $this->dispatch('modalClosing');
    }

    public function startShowAnimation()
    {
        $this->isVisible = true;
    }

    public function finalizeClose()
    {
        $this->modal = false;
    }

    public function moveCheckout()
    {
        $cart = new CartService();

        if (!$cart->hasProducts()) {
            return;
        }

        $order = Order::preparing($cart);
        $order->user_id = Auth::id() ?? 0;
        $order = $order->savePrepared();

        $cart->flushCart();
        Session::put('checkout', $order->id);

        return redirect()->route('checkout');
    }

    public function modalHasLogo()
    {
      $arr = [
        'cart', 
        'levels', 
        'product', 
        'refund',
        'refund-accept',
        'cancelsub',
        'cancelsub-accept',
        'funds',
        'funds-success',
        'funds-error',
        'withdraw',
        'withdraw-accept',
        'change-email',
        'change-email-accept',
        'twofa',
        'twofa-accept',
        'twofa-disable',
        'twofa-disable-accept',
        'payout-method',
        'delete-account',
        'delete-account-accept',
        'message',
        'contact',
        'social',
        'donate',
        'donate-accept',
        'donate-sub-accept',
        'donate-error',
        'promocodes',
        'order',
        'edit-contacts',
        'file-description',
        'delete-subscription',
        'delete-subscription-accept',
        'delete-product',
        'delete-product-accept',
        'payout-details',
      ];
      return !in_array($this->modal, $arr);
    }    

    public function modalMaxWidth()
    {
			if (in_array($this->modal, ['cart', 'levels'])) return '!max-w-none';
    
      if (in_array($this->modal, ['promocodes'])) return '!max-w-7xl';

      if (in_array($this->modal, ['product', 'twofa', 'report'])) return '!max-w-4xl';

      if (in_array($this->modal, ['delete-account'])) return '!max-w-3xl';

      if (in_array($this->modal, [
        'refund',
        'cancelsub',
        'withdraw',
        'twofa-accept',
        'twofa-disable-accept',
        'delete-account-accept',
      ])) return '!max-w-2xl';

      if (in_array($this->modal, [
        'refund-accept',
        'cancelsub-accept',
        'withdraw-accept',
        'change-email',
        'change-email-accept',
        'twofa-disable',
        'delete-account',
        'message',
        'social',
        'donate',
        'delete-subscription',
        'delete-product',
        'delete-product-accept',
      ])) return '!max-w-2xl';
      
      if (in_array($this->modal, ['funds', 'contact', 'payment-method'])) return '!max-w-xl';

      if (in_array($this->modal, ['payout-details'])) return '!max-w-3xl';

      return '';
    }    

    public function render()
    {
        return view('livewire.modals', [
          'modal' => $this->modal,
          'isVisible' => $this->isVisible,
          'args' => $this->args,
        ]);
    }
}
