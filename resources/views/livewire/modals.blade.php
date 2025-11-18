<div class="">
    <div class="fixed top-0 left-0 w-screen h-screen px-1.5 py-3 z-[1200] flex justify-center items-center cartMayBe
              bg-stone-900/50 overflow-y-scroll
                {{ $isVisible ? 'modal-fade-in' : ($this->inited ? 'modal-fade-out' : 'hidden') }} 
                @if ($this->inited && $this->modal == false) hidden @endif
                "
        wire:keydown.escape="closeModal" tabindex="0" x-data="{}" 
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
                      @endphp
                      @livewire($componentName, $componentParams, key($componentKey))
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
