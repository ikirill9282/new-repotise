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
                    if (['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept'].includes(modalName)) {
                        protectedModal = modalName;
                    }
                });
                
                // Сбрасываем защиту при закрытии модального окна пользователем и обновляем список продуктов
                window.addEventListener('modalClosing', () => {
                    const currentModal = @this.get('modal');
                    if (currentModal === protectedModal) {
                        protectedModal = null;
                    }
                    
                    // Если закрылось модальное окно delete-product-accept, отправляем событие для обновления списка продуктов
                    if (currentModal === 'delete-product-accept') {
                        // Задержка, чтобы модальное окно успело полностью закрыться перед обновлением списка
                        setTimeout(() => {
                            Livewire.dispatch('products-refresh');
                        }, 300);
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
                        $shouldIgnore = in_array($this->modal, ['delete-product-accept', 'delete-subscription-accept', 'cancelsub-accept']);
                      @endphp
                      <div @if($shouldIgnore) wire:ignore.self wire:key="modal-content-{{ $this->modal }}" @endif>
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
    Livewire.hook('morphed', function() {
    });
  </script>
@endscript
