<div class="">
    @php
      // Determine what modal to render
      if (!$isVisible && $this->closingModal) {
        $renderModal = $this->closingModal;
        $renderArgs = is_array($this->closingArgs) ? $this->closingArgs : [];
      } elseif ($isVisible && $this->modal) {
        $renderModal = $this->modal;
        $renderArgs = is_array($this->args) ? $this->args : [];
      } else {
        $renderModal = null;
        $renderArgs = [];
      }
      
      // Hide container immediately when closing (even if closingModal exists for animation)
      // This prevents showing empty popup when closing modal
      $shouldHide = !$isVisible;
      // Only show container if modal is visible (not closing)
      $hasContent = $isVisible && !empty($renderModal) && view()->exists('livewire.modals.' . $renderModal);
      
      $currentModal = $renderModal;
      $isCart = ($currentModal === 'cart');
      $containerJustify = $isCart ? 'justify-end' : 'justify-center';
      $containerItems = $isCart ? 'items-stretch' : 'items-center';
    @endphp
    @if ($hasContent)
    <div class="fixed top-0 left-0 w-screen h-screen z-50 flex {{ $containerJustify }} {{ $containerItems }} cartMayBe
              bg-stone-900/50
                {{ $isVisible ? 'modal-fade-in' : 'hidden' }}
                "
        wire:keydown.escape="closeModal" 
        wire:click.self="closeModal"
        tabindex="0" x-data="{}" 
        x-init="
                // Check if modal should be hidden on init
                const isVisible = @js($isVisible);
                const closingModal = @js($this->closingModal ?? null);
                if (!isVisible && !closingModal) {
                    const modalElement = document.querySelector('.cartMayBe');
                    if (modalElement) {
                        modalElement.classList.add('hidden');
                        modalElement.style.display = 'none';
                        modalElement.style.pointerEvents = 'none';
                    }
                    document.body.classList.remove('overflow-hidden');
                    document.body.style.pointerEvents = 'auto';
                }
                
                if (isVisible) {
                    document.body.classList.add('overflow-hidden');
                }
                if (@js($isVisible) && @js($modal) === 'cart') {
                    const cartOverlay = document.querySelector('.cartMayBe');
                    if (cartOverlay) {
                      cartOverlay.classList.add('goRight');
                    }
                    requestAnimationFrame(goRightCart);
                } else if (@js($modal) !== 'cart') {
                    // Remove goRight class if modal is not cart
                    document.querySelector('.cartMayBe')?.classList.remove('goRight');
                }

                // Attach listeners only once. This template re-renders often,
                // and without protection handlers accumulate (causing flickering/double timers).
                window.__tgModalsListenersAttached = window.__tgModalsListenersAttached || false;
                if (!window.__tgModalsListenersAttached) {
                  window.__tgModalsListenersAttached = true;

                  window.addEventListener('modalClosing', () => {
                      // Hide container immediately when closing
                      const modalElement = document.querySelector('.cartMayBe');
                      if (modalElement) {
                          modalElement.classList.add('hidden');
                          modalElement.style.display = 'none';
                          modalElement.style.pointerEvents = 'none';
                          // Also remove overflow-hidden from body immediately
                          document.body.classList.remove('overflow-hidden');
                          document.body.style.pointerEvents = 'auto';
                      }
                      
                      // Increase delay to ensure close animation completes before clearing
                      // Modal fade-out animation is 300ms, so we wait 350ms to be safe
                      setTimeout(() => {
                          @this.call('finalizeClose');
                          // Ensure container is hidden after finalizeClose
                          const modalElement = document.querySelector('.cartMayBe');
                          if (modalElement) {
                              modalElement.classList.add('hidden');
                              modalElement.style.display = 'none';
                              modalElement.style.pointerEvents = 'none';
                          }
                          document.body.classList.remove('overflow-hidden');
                          document.body.style.pointerEvents = 'auto';
                      }, 350);
                  }, { passive: true });

                  window.addEventListener('modal-closing-clean-url', () => {
                      const url = new URL(window.location.href);
                      url.searchParams.delete('modal');
                      window.history.replaceState({}, document.title, url.toString());
                  }, { passive: true });

                  // Handler for opening modal after previous one closes
                  let pendingModalToOpen = null;
                  let isOpeningModal = false;
                  
                  Livewire.on('openModalAfterClose', (event) => {
                      const modalName = Array.isArray(event) ? event[0] : event;
                      if (isOpeningModal) {
                          // If already opening modal, ignore repeated calls
                          return;
                      }
                      pendingModalToOpen = modalName;
                      isOpeningModal = true;
                      
                      // Wait for close animation (300ms) then open new window
                      setTimeout(() => {
                          if (pendingModalToOpen && !@this.get('isVisible')) {
                              @this.call('openModalAfterClose', pendingModalToOpen);
                              pendingModalToOpen = null;
                              // Reset flag after small delay
                              setTimeout(() => {
                                  isOpeningModal = false;
                              }, 100);
                          } else {
                              isOpeningModal = false;
                          }
                      }, 450);
                  });
                  
                  // Track modal opening to reset flag
                  Livewire.on('modal-opened', () => {
                      isOpeningModal = false;
                      pendingModalToOpen = null;
                  });

                  Livewire.on('startShowAnimation', () => {
                      @this.call('startShowAnimation')
                  });

                  Livewire.on('modal-opened', event => {
                      document.body.classList.add('overflow-hidden');
                      const modalName = event[0].modal;
                      const url = new URL(window.location.href);
                      url.searchParams.set('modal', modalName);
                      window.history.replaceState({}, document.title, url.toString());
                      const cartOverlay = document.querySelector('.cartMayBe');
                      if (modalName === 'cart') {
                          if (cartOverlay) {
                            cartOverlay.classList.add('goRight');
                          }
                          requestAnimationFrame(goRightCart);
                      } else {
                          cartOverlay?.classList.remove('goRight');
                      }
                      setTimeout(() => {
                        initCartSlider();
                      }, 10);
                  });
                  
                  // Protection from closing delete-product-accept modal on component updates
                  let protectedModal = null;
                  
                  Livewire.on('modal-opened', event => {
                      const modalName = event[0]?.modal;
                      // Protect success modals from auto-closing
                      if (['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept', 'delete-article-accept'].includes(modalName)) {
                          protectedModal = modalName;
                      }
                  });
                  
                  // Reset protection on user modal close and refresh products/articles list
                  let isUserClosingModal = false;
                  
                  // Track when user clicks close button
                  document.addEventListener('click', (e) => {
                      // Check if click target is modal close button
                      const target = e.target.closest('[wire\\:click], button, a');
                      if (target) {
                          const wireClick = target.getAttribute('wire:click');
                          const wireDispatch = target.getAttribute('wire:click.prevent');
                          if (wireClick && (wireClick.includes('closeModal') || wireClick.includes('$dispatch'))) {
                              const currentModal = @this.get('modal');
                              if (['delete-product-accept', 'delete-article-accept'].includes(currentModal)) {
                                  isUserClosingModal = true;
                                  // Save info that user is closing modal
                                  sessionStorage.setItem('userClosingModal', currentModal);
                              }
                          }
                      }
                  }, { passive: true });
                  
                  window.addEventListener('modalClosing', () => {
                    const currentModal = @this.get('modal');
                    const userClosingModal = sessionStorage.getItem('userClosingModal');
                    
                    // If delete-product-accept modal closed, dispatch event to refresh products list
                    if (currentModal === 'delete-product-accept' && userClosingModal === 'delete-product-accept') {
                        // Delay to let modal fully close before refreshing list
                        setTimeout(() => {
                            Livewire.dispatch('products-refresh');
                            sessionStorage.removeItem('userClosingModal');
                        }, 300);
                    }
                    
                    // If delete-article-accept modal closed, dispatch event to refresh articles list
                    if (currentModal === 'delete-article-accept' && userClosingModal === 'delete-article-accept') {
                        setTimeout(() => {
                            Livewire.dispatch('articles-refresh');
                            sessionStorage.removeItem('userClosingModal');
                        }, 300);
                    }
                    
                    if (currentModal === protectedModal && userClosingModal === currentModal) {
                        protectedModal = null;
                        sessionStorage.removeItem('userClosingModal');
                    }
                  }, { passive: true });
                  
                  window.addEventListener('modalClosing', () => {
                    // Keep position/scroll until animation completes,
                    // to avoid jump or empty state.
                    setTimeout(() => {
                      const modalElement = document.querySelector('.cartMayBe');
                      if (modalElement) {
                        modalElement.classList.remove('goRight');
                        // Ensure it's hidden
                        modalElement.classList.add('hidden');
                        modalElement.style.display = 'none';
                        modalElement.style.pointerEvents = 'none';
                      }
                      document.body.classList.remove('overflow-hidden');
                      document.body.style.pointerEvents = 'auto';
                    }, 320);
                  }, { passive: true });
                }
        ">
        @php
          // $renderModal already calculated above
          $justifyClass = ($renderModal === 'cart') ? 'justify-end' : 'justify-center';
        @endphp
        @if (!empty($renderModal))
          <div class="popUp-wrap flex {{ $justifyClass }} {{ $isCart ? 'items-stretch h-screen' : 'items-center h-full' }} min-w-full sm:min-w-lg">
              <div class="popUp-wrap w-full {{ $isCart ? 'h-full' : 'max-h-full' }} overflow-hidden">
                  <x-card
                    size="xs"
                    class="popUp__edit-contact popUp mx-auto !gap-0 !rounded-xl md:min-w-xl {{ $isCart ? 'h-screen' : 'max-h-full' }} overflow-y-auto overflow-x-hidden scrollbar-custom
                    {{ $isVisible ? 'modal-slide-in' : 'modal-slide-out' }}
                    {{ $this->modalMaxWidth() }}
                    "
                  >

                      {{-- CLOSE --}}
                      <div wire:click.prevent="closeModal" class="text-gray hover:text-active text-right hover:cursor-pointer">
                          @include('icons.close', ['class' => '!inline-block'])
                      </div>

                      {{-- LOGO --}}
                      @if($this->modalHasLogo())
                        <div class="logo text-center !mb-[10px] sm:!mb-[20px] lg:!mb-[30px]">
                            <a href="{{ route('home') }}"><img class="inline-block w-25 sm:!max-w-none"
                                    src="{{ asset('/assets/img/logo.svg') }}" alt=""></a>
                        </div>
                      @endif

                      {{-- CONTENT --}}
                      @if (view()->exists('livewire.modals.' . $renderModal))
                        @php
                          $componentName = 'modals.' . $renderModal;
                          $componentParams = is_array($renderArgs) ? $renderArgs : [];
                          $componentKey = 'modal-' . $renderModal . '-' . md5(json_encode($componentParams));
                          // Для модальных окон успеха используем wire:ignore.self, чтобы предотвратить закрытие при обновлении
                          $shouldIgnore = in_array($renderModal, ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept', 'delete-article-accept']);
                        @endphp
                        <div @if($shouldIgnore) wire:ignore.self @endif wire:key="modal-content-{{ $renderModal }}">
                          @livewire($componentName, $componentParams, key($componentKey))
                        </div>
                      @endif
                </x-card>
              </div>
          </div>
        @endif
    </div>
    @endif
</div>


@script()
  <script>
    Livewire.hook('morphed', function({ el, component }) {
      // Сохраняем состояние защищенных модальных окон при обновлении компонента
      const protectedModals = ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept', 'delete-article-accept'];
      
      // Находим компонент Modals через DOM элемент
      const modalElement = document.querySelector('.cartMayBe');
      if (!modalElement) return;
      
      // Ищем wire:id атрибут через все элементы
      let componentId = null;
      const allElements = document.querySelectorAll('*');
      for (let el of allElements) {
        if (el.hasAttribute && el.hasAttribute('wire:id')) {
          const id = el.getAttribute('wire:id');
          if (id && id.includes('modals')) {
            componentId = id;
            break;
          }
        }
      }
      
      if (!componentId) return;
      
      try {
        const modalComponent = Livewire.find(componentId);
        if (!modalComponent || typeof modalComponent.get !== 'function') return;
        
        const currentModal = modalComponent.get('modal');
        
        if (protectedModals.includes(currentModal)) {
          // Если защищенное модальное окно открыто, убеждаемся, что оно остается открытым
          // Принудительно устанавливаем состояние, даже если компонент пытается его изменить
          modalComponent.set('isVisible', true);
          modalComponent.set('inited', true);
          if (typeof modalComponent.call === 'function') {
            modalComponent.call('startShowAnimation');
          }
        }
      } catch (e) {
        // Игнорируем ошибки при доступе к компоненту
        console.debug('Error accessing modal component:', e);
      }
    });
    
    // Дополнительная защита: предотвращаем закрытие защищенных модальных окон при обновлении
    Livewire.hook('message.processed', ({ component, message }) => {
      const protectedModals = ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept', 'delete-article-accept'];
      
      // Находим компонент Modals через DOM элемент
      const modalElement = document.querySelector('.cartMayBe');
      if (!modalElement) return;
      
      // Ищем wire:id атрибут через все элементы
      let componentId = null;
      const allElements = document.querySelectorAll('*');
      for (let el of allElements) {
        if (el.hasAttribute && el.hasAttribute('wire:id')) {
          const id = el.getAttribute('wire:id');
          if (id && id.includes('modals')) {
            componentId = id;
            break;
          }
        }
      }
      
      if (!componentId) return;
      
      try {
        const modalComponent = Livewire.find(componentId);
        if (!modalComponent || typeof modalComponent.get !== 'function') return;
        
        const currentModal = modalComponent.get('modal');
        
        if (protectedModals.includes(currentModal)) {
          // Если защищенное модальное окно открыто, убеждаемся, что оно остается открытым
          // Принудительно устанавливаем состояние, даже если компонент пытается его изменить
          modalComponent.set('isVisible', true);
          modalComponent.set('inited', true);
          if (typeof modalComponent.call === 'function') {
            modalComponent.call('startShowAnimation');
          }
        }
      } catch (e) {
        // Игнорируем ошибки при доступе к компоненту
        console.debug('Error accessing modal component:', e);
      }
    });
  </script>
@endscript
