<div class="relative">
  <h2 class="text-2xl font-bold text-gray-900 !mb-6 text-center select-none">Welcome!</h2>
  
  <form wire:submit.prevent="attempt" class="!space-y-4 !mb-10">
    @csrf

    {{-- STEP 1 --}}
    @if($this->step == 1)

      <div class="!mb-6">
        <x-form.input wire:model="form.email" name="email" type="email" placeholder="Email" :tooltipModal="true"></x-form.input>
      </div>

    {{-- STEP 2 --}}
    @elseif ($this->step == 2)

      <div class="">
        <x-form.input wire:model="form.email" name="email" type="email" placeholder="Email" :tooltipModal="true" autocomplete="one-time-code"></x-form.input>
      </div>
      
      <div x-data="{ type: 'password' }" class="">
        <x-form.input wire:model="form.password" name="password" x-bind:type="type" placeholder="Password" :tooltip="false" autocomplete="current-password">
          <x-slot name="icon">
            <div x-on:click="() => type = (type == 'password') ? 'text' : 'password' " class="absolute top-1/2 right-3 translate-y-[-50%] hover:cursor-pointer">
              <img src="{{ asset('assets/img/icons/eye.svg') }}" alt="Eye" />
            </div>
          </x-slot>
        </x-form.input>
      </div>
      
      @if($this->getUser()?->twofa)
        <div class="!mb-3">
          <x-form.input wire:model="form.2fa" name="2fa" placeholder="Authenticator App Code" />
        </div>

        <div class="">
          <x-form.checkbox wire:model="form.backup" label="Use Backup Code" />
        </div>
      @endif
      
      @if($this->showRecaptchaV2)
        <div class="!mb-4" id="recaptcha-v2-container"></div>
        <input type="hidden" wire:model="form.recaptcha_token" name="recaptcha_token" id="recaptcha_token">
        @error('form.recaptcha_token')
          <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
      @endif

    @endif

    <div class="flex justify-between items-center !gap-2">
      <x-btn wire:click.prevent="$dispatch('closeModal')" class="basis-1/3" gray>Cancel</x-btn>
      <x-btn type="button" wire:click.prevent="attempt" class="basis-2/3" id="login-submit-btn" wire:target="attempt">Continue</x-btn>
    </div>

    <div class="flex items-center justify-center">
      <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'reset-password' })" href="#" class="!border-0">Forgot password?</x-link>
    </div>
  </form>

  <div class="flex justify-center items-center !gap-2 !mb-6">
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
    <div class="text-gray shrink-0 text-sm">Other log in options.</div>
    <div class="bg-[#F3F2F2] h-[1px] w-full"></div>
  </div>

  <div class="flex justify-between items-center !gap-2 text-gray">
    <div wire:click.prevent="googleAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/google.svg') }}" alt="Google"></div>
      <div class="transition group-hover:text-active !mt-0.5">Google</div>
    </div>
    <div wire:click.prevent="fbAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/facebook.svg') }}" alt="Facebook"></div>
      <div class="transition group-hover:text-active !mt-0.5">Facebook</div>
    </div>
    <div wire:click.prevent="xAuth" class="group w-full flex justify-center items-cetner !gap-3 border-1 rounded-lg border-[#F3F2F2] !p-3 transition hover:cursor-pointer hover:border-active">
      <div class=""><img src="{{ asset('assets/img/icons/xai.svg') }}" alt="XAI"></div>
      <div class="transition group-hover:text-active !mt-0.5">X (Twitter)</div>
    </div>
  </div>
</div>

@push('js')
<script>
  @php
    $recaptchaSiteKey = config('services.recaptcha.site_key');
    $componentId = $this->getId();
  @endphp
  
  @if($recaptchaSiteKey)
  // reCAPTCHA v3 for login - execute automatically before attempt
  (function() {
    const componentId = @js($componentId);
    const recaptchaSiteKey = @js($recaptchaSiteKey);
    
    function executeRecaptchaV3() {
      if (!window.Livewire || typeof grecaptcha === 'undefined') {
        return;
      }
      
      const component = window.Livewire.find(componentId);
      if (!component) {
        return;
      }
      
      const step = component.get('step');
      const showRecaptchaV2 = component.get('showRecaptchaV2') ?? false;
      
      // Only execute reCAPTCHA v3 if needed (step 2, no v2)
      if (step === 2 && !showRecaptchaV2) {
        grecaptcha.ready(function() {
          grecaptcha.execute(recaptchaSiteKey, {action: 'login'}).then(function(token) {
            component.set('recaptcha_token', token);
          }).catch(function(error) {
            console.error('reCAPTCHA error:', error);
          });
        });
      }
    }
    
    // Execute reCAPTCHA when component updates to step 2
    if (window.Livewire && window.Livewire.hook) {
      window.Livewire.hook('component.updated', ({ component }) => {
        if (component.id === componentId) {
          executeRecaptchaV3();
        }
      });
    } else {
      document.addEventListener('livewire:init', () => {
        if (window.Livewire && window.Livewire.hook) {
          window.Livewire.hook('component.updated', ({ component }) => {
            if (component.id === componentId) {
              executeRecaptchaV3();
            }
          });
        }
      });
    }
  })();
  
  // reCAPTCHA v2 callback - prevent duplicate rendering
  (function() {
    const componentId = @js($componentId);
    const recaptchaSiteKey = @js($recaptchaSiteKey);
    let recaptchaWidgetId = null;
    let isRendering = false;
    let lastStep = null;
    
    function renderRecaptchaV2() {
      // Prevent concurrent rendering
      if (isRendering || typeof grecaptcha === 'undefined') {
        return;
      }
      
      const livewireComponent = window.Livewire.find(componentId);
      if (!livewireComponent) {
        return;
      }
      
      const showRecaptchaV2 = livewireComponent.get('showRecaptchaV2');
      const currentStep = livewireComponent.get('step');
      const container = document.getElementById('recaptcha-v2-container');
      
      if (!showRecaptchaV2 || !container) {
        // Reset widget ID if container is removed or reCAPTCHA is hidden
        if (recaptchaWidgetId !== null) {
          try {
            grecaptcha.reset(recaptchaWidgetId);
          } catch (e) {
            // Ignore reset errors
          }
          recaptchaWidgetId = null;
        }
        return;
      }
      
      // Check if widget already exists in DOM - use multiple checks
      const hasGRecaptcha = container.querySelector('.g-recaptcha') !== null;
      const hasIframe = container.querySelector('iframe[src*="recaptcha"]') !== null;
      const hasWidgetIdAttr = container.getAttribute('data-widget-id') !== null;
      const hasWidgetInDOM = hasGRecaptcha || hasIframe || hasWidgetIdAttr;
      
      // If widget exists in DOM, don't render again
      if (hasWidgetInDOM) {
        // Update widget ID if we have it but container doesn't have the attribute
        if (recaptchaWidgetId !== null && !hasWidgetIdAttr) {
          container.setAttribute('data-widget-id', recaptchaWidgetId);
        }
        return;
      }
      
      // Widget doesn't exist, render it
      isRendering = true;
      lastStep = currentStep;
      
      // Use requestAnimationFrame to avoid forced reflow
      requestAnimationFrame(() => {
        // Double-check that we're still supposed to render and widget doesn't exist
        const containerCheck = document.getElementById('recaptcha-v2-container');
        if (!containerCheck) {
          isRendering = false;
          return;
        }
        
        const stillHasWidget = containerCheck.querySelector('.g-recaptcha') !== null || 
                              containerCheck.querySelector('iframe[src*="recaptcha"]') !== null ||
                              containerCheck.getAttribute('data-widget-id') !== null;
        
        if (stillHasWidget) {
          isRendering = false;
          return;
        }
        
        try {
          // Clear container before rendering
          container.innerHTML = '';
          
          recaptchaWidgetId = grecaptcha.render(container, {
            'sitekey': recaptchaSiteKey,
            'callback': function(token) {
              const comp = window.Livewire.find(componentId);
              if (comp) {
                comp.set('form.recaptcha_token', token);
              }
            },
            'expired-callback': function() {
              const comp = window.Livewire.find(componentId);
              if (comp) {
                comp.set('form.recaptcha_token', null);
              }
            },
            'error-callback': function() {
              const comp = window.Livewire.find(componentId);
              if (comp) {
                comp.set('form.recaptcha_token', null);
              }
            }
          });
          
          // Store widget ID in container attribute for future checks
          if (recaptchaWidgetId !== null) {
            container.setAttribute('data-widget-id', recaptchaWidgetId);
          }
        } catch (error) {
          // If error is "already rendered", widget might already exist
          if (error.message && error.message.includes('already been rendered')) {
            // Check if widget actually exists
            const existingWidget = container.querySelector('.g-recaptcha, iframe[src*="recaptcha"]');
            if (existingWidget) {
              // Widget exists, mark as rendered
              container.setAttribute('data-widget-id', 'existing');
            } else {
              // Clear and try again after a delay
              container.innerHTML = '';
              setTimeout(() => {
                isRendering = false;
                renderRecaptchaV2();
              }, 500);
              return;
            }
          } else {
            console.error('reCAPTCHA render error:', error);
          }
          recaptchaWidgetId = null;
        } finally {
          isRendering = false;
        }
      });
    }
    
    function setupRecaptchaV2() {
      if (!window.Livewire || !window.Livewire.hook) {
        return;
      }
      
      // Use a single hook registration per component
      const hookKey = '__recaptchaV2Hook_' + componentId;
      if (window[hookKey]) {
        return;
      }
      window[hookKey] = true;
      
      window.Livewire.hook('morph.updated', ({ el, component }) => {
        // Only process if this is our component
        const livewireComponent = window.Livewire.find(componentId);
        if (!livewireComponent) {
          return;
        }
        
        // Check if recaptcha container exists (it might be in the updated element or its children)
        const container = document.getElementById('recaptcha-v2-container');
        if (!container) {
          return;
        }
        
        // Check if container is inside the updated element or the updated element is inside container
        const isRelevant = el.contains(container) || container.contains(el) || el === container;
        if (!isRelevant) {
          return;
        }
        
        // Check if widget still exists after morph
        const hasWidget = container.querySelector('.g-recaptcha') !== null || 
                         container.querySelector('iframe[src*="recaptcha"]') !== null;
        
        // Only re-render if widget is missing
        if (!hasWidget) {
          // Use requestAnimationFrame with a delay to ensure DOM is fully updated
          requestAnimationFrame(() => {
            setTimeout(() => {
              renderRecaptchaV2();
            }, 150);
          });
        }
      });
      
      // Also render on component update
      window.Livewire.hook('component.updated', ({ component }) => {
        if (component.id === componentId) {
          const container = document.getElementById('recaptcha-v2-container');
          if (container) {
            const hasWidget = container.querySelector('.g-recaptcha') !== null || 
                             container.querySelector('iframe[src*="recaptcha"]') !== null;
            
            // Only re-render if widget is missing
            if (!hasWidget) {
              requestAnimationFrame(() => {
                setTimeout(() => {
                  renderRecaptchaV2();
                }, 150);
              });
            }
          }
        }
      });
    }
    
    // Define the function globally for onRecaptchaV2Load callback
    window.onRecaptchaV2Load = function() {
      // Setup when Livewire is available
      if (window.Livewire) {
        setupRecaptchaV2();
        // Try to render immediately
        setTimeout(() => renderRecaptchaV2(), 200);
      } else {
        document.addEventListener('livewire:init', function() {
          setupRecaptchaV2();
          setTimeout(() => renderRecaptchaV2(), 200);
        }, { once: true });
      }
    };
    
    // Also try to setup immediately if both are available
    if (window.Livewire && typeof grecaptcha !== 'undefined') {
      setupRecaptchaV2();
      setTimeout(() => renderRecaptchaV2(), 200);
    }
    
    // Also check on DOMContentLoaded
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() {
        if (window.Livewire && typeof grecaptcha !== 'undefined') {
          setTimeout(() => {
            setupRecaptchaV2();
            renderRecaptchaV2();
          }, 300);
        }
      });
    }
  })();
  @endif
</script>
@endpush
