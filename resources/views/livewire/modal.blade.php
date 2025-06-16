<div>
  <div
    id="modal"
    class="fixed {{ $this->active ? '' : 'hidden' }} top-0 left-0 z-[9999] w-screen min-h-screen bg-gray-100 flex items-center justify-center p-2 md:!p-4">
    <div class="relative w-full md:w-auto">
      <div wire:click="close" class="close_modal absolute top-1.5 right-1.5 p-2 bg-white stroke-gray-400 transition hover:cursor-pointer hover:!stroke-black z-40">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none" class="rotate-45">
          <path d="M8.19982 2.84314C8.19982 7.26142 8.19982 9.73857 8.19982 14.1568M2.54297 8.49999C6.96125 8.49999 9.4384 8.49999 13.8567 8.49999" stroke="inherit" stroke-width="1.5" stroke-linecap="round"/>
          <script xmlns=""/>
        </svg>
      </div>
      @include("livewire.modal.$this->view")
    </div>
    
  </div>
</div>

@script
<script>

  Livewire.hook('morphed', ({ el, component }) => {
      initModal();
      
      if (!component.reactive.active && component.reactive.open) {
        $('#modal').css({'display': 'flex'}).hide().fadeIn();
        Livewire.dispatch('activate');
      }

      if (component.reactive.active && !component.reactive.open) {
        $('#modal').fadeOut();
        Livewire.dispatch('deactivate');
      }

      document.querySelector('#modal').querySelectorAll('.counter').forEach(counter => {
        const minusBtn = counter.querySelector('.minus');
        const plusBtn = counter.querySelector('.plus');
        const countEl = counter.querySelector('.count');
      
        let count = parseInt(countEl.textContent);
      
        plusBtn.addEventListener('click', function() {
          count++;
          countEl.textContent = count;
          counterChanged(this.closest('.counter'), count);
        });
      
        minusBtn.addEventListener('click', function() {
          if (count > 1) {
            count--;
            countEl.textContent = count;
            counterChanged(this.closest('.counter'), count);
          }
        });
      });
  });
</script>
@endscript