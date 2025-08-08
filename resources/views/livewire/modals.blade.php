<div class="">
    <div class="fixed top-0 left-0 w-screen h-screen px-1.5 py-3 z-[1200] flex justify-center items-center bg-stone-900/50 overflow-y-scroll
                {{ $isVisible ? 'modal-fade-in' : ($this->inited ? 'modal-fade-out' : 'hidden') }} @if ($this->inited && $this->modal == false) hidden @endif"
        wire:keydown.escape="closeModal" tabindex="0" x-data="{}" x-init="window.addEventListener('modalClosing', () => {
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
            const modalName = event[0].modal;
            const url = new URL(window.location.href);
            url.searchParams.set('modal', modalName);
            window.history.replaceState({}, document.title, url.toString());
            setTimeout(() => {
              initCartSlider();
            }, 10);
        });">
        <div class="popUp-wrap flex justify-center items-center h-full min-w-full sm:min-w-lg">
            <div class="popUp-wrap w-full max-h-full overflow-y-scroll">
                <dialog
                  class="popUp__edit-contact popUp !gap-[10px] sm:!gap-[20px] lg:!gap-[30px] mx-auto
                  {{ $isVisible ? 'modal-slide-in' : 'modal-slide-out' }}
                  overflow-hidden
                  {{ $this->modal == 'cart' ? '!max-w-none' : '' }}
                  "
                >
                    <div wire:click.prevent="closeModal" class="popUp__cross w-5 h-5">
                        <svg class="" width="16" height="16" viewBox="0 0 16 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L15 15M1 15L15 1" stroke="#A4A0A0" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                    </div>
                    @if(!in_array($this->modal, ['cart']))
                      <div class="logo text-center">
                          <a href="{{ route('home') }}"><img class="inline-block w-25 sm:!max-w-none"
                                  src="{{ asset('/assets/img/logo.svg') }}" alt=""></a>
                      </div>
                    @endif
                    @if (view()->exists('livewire.modals.' . $this->modal))
                        @livewire('modals.' . $this->modal, key($this->modal), ['args' => $this->args])
                    @endif
              </dialog>
            </div>
        </div>
    </div>
</div>
