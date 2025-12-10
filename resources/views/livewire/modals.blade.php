<div class="">
    <div class="fixed top-0 left-0 w-screen h-screen px-1.5 py-3 z-[1200] flex justify-center items-center cartMayBe
              bg-stone-900/50 overflow-y-scroll
                {{ $isVisible ? 'modal-fade-in' : ($this->inited ? 'modal-fade-out' : 'hidden') }} 
                @if ($this->inited && $this->modal == false) hidden @endif
                "
        wire:keydown.escape="closeModal" 
        wire:click.self="closeModal"
        tabindex="0" x-data="{}" 
        x-init="
								if (@js($isVisible)) {
										document.body.classList.add('overflow-hidden');
								}
								if (@js($isVisible) && @js($modal) === 'cart') {
										requestAnimationFrame(goRightCart);
								}

								
								window.addEventListener('modalClosing', () => {
                    setTimeout(() => {
                        @this.call('finalizeClose')
                    }, 300)
                });
                window.addEventListener('modal-closing-clean-url', () => {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('modal');
                    window.history.replaceState({}, document.title, url.toString());
                });
                // Обработчик для открытия модального окна после закрытия предыдущего
                let pendingModalToOpen = null;
                let isOpeningModal = false;
                
                Livewire.on('openModalAfterClose', (event) => {
                    const modalName = Array.isArray(event) ? event[0] : event;
                    if (isOpeningModal) {
                        // Если уже открываем модальное окно, игнорируем повторные вызовы
                        return;
                    }
                    pendingModalToOpen = modalName;
                    isOpeningModal = true;
                    
                    // Ждем завершения анимации закрытия (300ms) и затем открываем новое окно
                    setTimeout(() => {
                        if (pendingModalToOpen && !@this.get('isVisible')) {
                            @this.call('openModalAfterClose', pendingModalToOpen);
                            pendingModalToOpen = null;
                            // Сбрасываем флаг через небольшую задержку
                            setTimeout(() => {
                                isOpeningModal = false;
                            }, 100);
                        } else {
                            isOpeningModal = false;
                        }
                    }, 450);
                });
                
                // Отслеживаем открытие модального окна, чтобы сбросить флаг
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
                    if (modalName === 'cart') {
        							requestAnimationFrame(goRightCart);

										} else {
											document.querySelector('.cartMayBe')?.classList.remove('goRight');

										}
                    setTimeout(() => {
                      initCartSlider();
                    }, 10);
                });
                
                // Защита от закрытия модального окна delete-product-accept при обновлении компонентов
                let protectedModal = null;
                
                Livewire.on('modal-opened', event => {
                    const modalName = event[0]?.modal;
                    // Защищаем модальные окна успеха от автоматического закрытия
                    if (['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept', 'delete-article-accept'].includes(modalName)) {
                        protectedModal = modalName;
                    }
                });
                
                // Сбрасываем защиту при закрытии модального окна пользователем и обновляем список продуктов/статей
                let isUserClosingModal = false;
                
                // Отслеживаем, когда пользователь нажимает кнопку закрытия
                document.addEventListener('click', (e) => {
                    // Проверяем, является ли цель клика кнопкой закрытия модального окна
                    const target = e.target.closest('[wire\\:click], button, a');
                    if (target) {
                        const wireClick = target.getAttribute('wire:click');
                        const wireDispatch = target.getAttribute('wire:click.prevent');
                        if (wireClick && (wireClick.includes('closeModal') || wireClick.includes('$dispatch'))) {
                            const currentModal = @this.get('modal');
                            if (['delete-product-accept', 'delete-article-accept'].includes(currentModal)) {
                                isUserClosingModal = true;
                                // Сохраняем информацию о том, что пользователь закрывает модальное окно
                                sessionStorage.setItem('userClosingModal', currentModal);
                            }
                        }
                    }
                });
                
                window.addEventListener('modalClosing', () => {
                    const currentModal = @this.get('modal');
                    const userClosingModal = sessionStorage.getItem('userClosingModal');
                    
                    // Если закрылось модальное окно delete-product-accept, отправляем событие для обновления списка продуктов
                    if (currentModal === 'delete-product-accept' && userClosingModal === 'delete-product-accept') {
                        // Задержка, чтобы модальное окно успело полностью закрыться перед обновлением списка
                        setTimeout(() => {
                            Livewire.dispatch('products-refresh');
                            sessionStorage.removeItem('userClosingModal');
                        }, 300);
                    }
                    
                    // Если закрылось модальное окно delete-article-accept, отправляем событие для обновления списка статей
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
                });
                
								window.addEventListener('modalClosing', () => {
									document.querySelector('.cartMayBe')?.classList.remove('goRight');
									document.body.classList.remove('overflow-hidden');
								});
								"
								
        >
        <div class="popUp-wrap flex justify-center items-center h-full min-w-full sm:min-w-lg">
            <div class="popUp-wrap w-full max-h-full overflow-y-auto overflow-x-hidden scrollbar-custom">
                <x-card
                  size="xs"
                  class="popUp__edit-contact popUp mx-auto !gap-0 !rounded-xl md:min-w-xl
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
                    @if (view()->exists('livewire.modals.' . $this->modal))
                      @php
                        $componentName = 'modals.' . $this->modal;
                        $componentParams = is_array($this->args) ? $this->args : [];
                        $componentKey = 'modal-' . $this->modal . '-' . md5(json_encode($componentParams));
                        // Для модальных окон успеха используем wire:ignore.self, чтобы предотвратить закрытие при обновлении
                        $shouldIgnore = in_array($this->modal, ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept', 'delete-article-accept']);
                      @endphp
                      <div @if($shouldIgnore) wire:ignore.self @endif wire:key="modal-content-{{ $this->modal }}">
                        @livewire($componentName, $componentParams, key($componentKey))
                      </div>
                    @endif
              </x-card>
            </div>
        </div>
    </div>
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
