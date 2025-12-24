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
    // Keep last modal during close animation to prevent "empty popup" flashes
    public ?string $closingModal = null;
    public array $closingArgs = [];

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
      'delete-product',
      'delete-article',
      // Модальные окна успеха (delete-*-accept, delete-subscription-accept, delete-product-accept) 
      // НЕ должны быть в oneTime, чтобы они не закрывались автоматически при обновлении компонентов
    ];

    // Защищенные модальные окна, которые не должны закрываться при обновлении компонентов
    protected $protectedModals = ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept', 'delete-article-accept'];
    
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
          // Не сбрасываем защищенные модальные окна при mount
          // Если текущее модальное окно защищено, сохраняем его состояние
          if (!in_array($this->modal, $this->protectedModals)) {
              $this->isVisible = false;
          }
      }
      
      $this->previousModal = $this->modal;
      $this->previousIsVisible = $this->isVisible;
    }
    
    public function hydrate()
    {
      // При гидратации компонента сохраняем состояние защищенных модальных окон
      if (in_array($this->modal, $this->protectedModals)) {
          $this->isVisible = true;
          $this->inited = true;
      }
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
      // Обработка случая, когда передается массив с ключами modalName и args
      if (is_array($modalName) && isset($modalName['modalName'])) {
        $args = $modalName['args'] ?? [];
        $modalName = $modalName['modalName'];
      }
      
      // Убеждаемся, что modalName - это строка
      if (!is_string($modalName)) {
        return;
      }
      
      $this->args = is_array($args) ? $args : [];
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
      if (!in_array($modalName, $this->oneTime) || in_array($modalName, ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept', 'delete-article-accept'])) {
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

        // Preserve current modal content for the duration of the close animation,
        // even if something clears $this->modal during the Livewire update cycle.
        // Set closingModal BEFORE clearing modal to prevent empty popup flash
        // Only set closingModal if there's actually a modal to close
        if ($modalToClose) {
            $this->closingModal = $modalToClose;
            $this->closingArgs = is_array($this->args) ? $this->args : [];
        } else {
            // If there's no modal to close, clear closingModal immediately
            // This ensures the container is hidden right away
            $this->closingModal = null;
            $this->closingArgs = [];
        }
        
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
        
        // Set isVisible to false, but DON'T clear modal yet - let finalizeClose() do it after animation
        $this->isVisible = false;
        $this->dispatch('modal-closing-clean-url');
        $this->dispatch('modalClosing');
    }
    
    public function render()
    {
        // При рендеринге сохраняем состояние защищенных модальных окон
        if (in_array($this->modal, $this->protectedModals)) {
            if (!$this->isVisible) {
                $this->isVisible = true;
            }
            if (!$this->inited) {
                $this->inited = true;
            }
        }
        
        return view('livewire.modals', [
          'modal' => $this->modal,
          'isVisible' => $this->isVisible,
          'args' => $this->args,
        ]);
    }

    public function startShowAnimation()
    {
        $this->isVisible = true;
    }

    public function finalizeClose()
    {
        // After the close animation ends, fully reset modal state.
        // Clear closingModal first, then modal, to prevent empty popup flash
        $this->closingModal = null;
        $this->closingArgs = [];
        $this->isVisible = false;
        $this->modal = null;
        $this->args = [];
        $this->inited = false;
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
        'delete-article',
        'delete-article-accept',
      ])) return '!max-w-2xl';
      
      // Funds modal is wider to fit payment + summary blocks
      if (in_array($this->modal, ['funds'])) return '!max-w-3xl';
      
      if (in_array($this->modal, ['contact', 'payment-method'])) return '!max-w-xl';

      if (in_array($this->modal, ['payout-details'])) return '!max-w-3xl';

      return '';
    }
}
