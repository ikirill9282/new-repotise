<div>

    @push('head')
      <script src="https://js.stripe.com/v3/"></script>
    @endpush

    @if ($product)
        <section class="placing_order">
            @include('site.components.breadcrumbs', [
                'current_name' => 'Checkout',
            ])
            <div class="container">
              <div class="about_block !items-stretch">
                <div class="left_form">
                    <div>
                        <div class="!mb-3">
                          <x-form.input wire:model="form.username" name="username" placeholder="Your Full Name" tooltipText="Enter your full name as it appears on your billing information." />
                        </div>
                        <div class="!mb-6">
                          <x-form.input wire:model="form.email" name="email" type="email" placeholder="Your Email" tooltipText="Your order confirmation will be sent here." />
                        </div>

                        @if (!is_null($paymentMethods) && !$paymentMethods->isEmpty())
                          <div class="!mb-6">
                            @foreach($paymentMethods as $pm)
                              @if($pm->type == 'card')
                                <div class="!mb-3">
                                  <x-form.payment-method
                                    wire:model="form.paymentMethod"
                                    label="Card" 
                                    :brand="ucfirst($pm->card->brand)"
                                    :last4="$pm->card->last4"
                                    :editor="false"
                                    :value="$pm->id"
                                  />
                                </div>
                              @endif
                            @endforeach
                          </div>
                        @endif

                        <div class="@if($product->subprice->getPeriodPrice($this->period) <= 0) hidden @endif">
                          <div wire:ignore>
                              <div id="payment" class="mb-4"></div>
                          </div>
                        </div>

                        <div class="flex flex-col justify-start items-stretch text-gray !gap-3 !pb-3 !mb-3 border-b border-active">
                            <div class="flex justify-between items-center w-full">
                                <span>Subtotal</span>
                                <span>{{ currency($product->subprice->getPeriodPrice($this->period)) }}</span>
                            </div>
                            <div class="flex justify-between items-center w-full">
                                <span>Discount</span>
                                <span class="{{ $this->discount > 0 ? '!text-emerald-500' : '' }}">
                                  -<span class="cart-discount">{{ currency($this->discount) }}</span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center w-full">
                                <span>Tax</span>
                                <span class="color_red">
                                  <span class="">{{ currency($this->tax) }}</span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center w-full text-black">
                                <span>Total</span>
                                <span>
                                  <span class="">{{ currency($product->subprice->getPeriodPrice($this->period)) }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="!mb-4">
                          <x-btn 
                            id="payment-btn" 
                            type="button" 
                            class="!max-w-none flex items-center justify-center gap-2"
                          >
                            <span data-role="label">Confirm Payment</span>
                            <span data-role="spinner" class="inline-flex" style="display: none;">
                              <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                                <style>
                                  .spinner-circle {
                                    opacity: 0.25;
                                  }
                                  .spinner-path {
                                    transform-origin: center;
                                    animation: spinner-rotate .75s linear infinite;
                                  }
                                  @keyframes spinner-rotate {
                                    100% {
                                      transform: rotate(360deg);
                                    }
                                  }
                                </style>
                                <path class="spinner-circle" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" />
                                <path class="spinner-path" d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                              </svg>
                            </span>
                          </x-btn>
                        </div>

                        <div class="text-sm w-full !mb-6 text-gray group">By confirm your payment, you agree to our 
                          <x-link
                            class="!border-none group-has-[a]:!text-active"
                            href="{{ url('/all-policies') }}"
                          >
                            Terms of Service & Privacy Policy.
                          </x-link>
                        </div>
                        <div class="text-sm flex justify-between items-center">
                            <x-link class="!border-none" href="{{ $product->makeUrl() }}">Back</x-link>
                            <x-link class="!border-none" href="{{ url('/help-center') }}">Need Help?</x-link>
                        </div>
                    </div>
                </div>
                <div class="right_orders !h-auto">
                  <div class="title_block">
                      <h3>Your order</h3>
                      <p>Items <span>(<span class="cart-counter">1</span>)</span></p>
                  </div>
                  <div class="items_group">
                      {{-- @foreach ($order->order_products as $order_product) --}}
                        <div class="item">
                          <img src="{{ url($product->preview->image) }}" alt="Product Preview" class="order_img">
                        <div class="description_orders">
                        <div class="flex justify-start items-start flex-col !gap-1 !mb-2">
                            <h4 class="!text-base group">
                              <x-link class="!border-none group-has-[a]:!text-black" target="_blank" href="{{ $product->makeUrl() }}">{{ $product->title }}</x-link>
                            </h4>
                            <h5 class="flex justify-end items-center !gap-1 !text-sm">
                              <span>{{ currency($product->subprice->getPeriodPrice($this->period)) }}</span>
                              <span>per {{ $this->period }}</span>
                            </h5>
                        </div>
                        <p>{{ $product->categories->pluck('title')->join(', ') }}</p>
                      {{-- @endforeach --}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </section>
    @else
      <div
        class="container empty-container !pt-10 !pb-10 {{ $product ? 'hidden' : '' }}">
        @include('site.components.favorite.empty', [
          'text' => 'Cart',
          'class' => 'empty-cart',
        ])
      </div>
    @endif
  </div>


@script
  <script>
      const stripe = Stripe(
        'pk_test_51QyRYMAcKvFfYWUGHWNhmA3IueKw7pitQONcJire1VVLx4t36rfGx54OB78EFZj6kKaS12M6GmzsOofOzfSjApKS00B8mwb7tR'
      );
      const paymentErrorUrl = '{{ route('payment.error') }}';
      const redirectToPaymentError = (code = null, declineCode = null) => {
        try {
          const target = new URL(paymentErrorUrl, window.location.origin);
          if (code) {
            target.searchParams.set('reason', code);
          }
          if (declineCode) {
            target.searchParams.set('decline_reason', declineCode);
          }
          window.location.href = target.toString();
        } catch (err) {
          window.location.href = paymentErrorUrl;
        }
      };
      const clientSecret = '{{ $intent->client_secret }}';
      const elements = stripe.elements({clientSecret});
      const paymentMethods = elements.create('payment');
      paymentMethods.mount("#payment");

      const btn = document.getElementById('payment-btn');
      const togglePaymentButtonLoading = (isLoading) => {
        if (!btn) {
          return;
        }
        const labelEl = btn.querySelector('[data-role="label"]');
        const spinnerEl = btn.querySelector('[data-role="spinner"]');

        if (isLoading) {
          btn.dataset.loading = 'true';
          btn.setAttribute('aria-busy', 'true');
          btn.setAttribute('aria-disabled', 'true');
          btn.classList.add('pointer-events-none');
          if ('disabled' in btn) {
            btn.disabled = true;
          }
          if (labelEl) {
            if (!btn.dataset.originalLabel) {
              btn.dataset.originalLabel = labelEl.textContent?.trim() ?? '';
            }
            labelEl.textContent = 'Processing...';
          }
          if (spinnerEl) {
            spinnerEl.style.display = '';
          }
        } else {
          btn.dataset.loading = 'false';
          btn.removeAttribute('aria-busy');
          btn.removeAttribute('aria-disabled');
          btn.classList.remove('pointer-events-none');
          if ('disabled' in btn) {
            btn.disabled = false;
          }
          const originalText = btn.dataset.originalLabel;
          if (labelEl && typeof originalText === 'string') {
            labelEl.textContent = originalText;
          }
          if (spinnerEl) {
            spinnerEl.style.display = 'none';
          }
        }
      };

      if (btn) {
        btn.dataset.loading = 'false';
        btn.addEventListener('click', async (evt) => {
          evt.preventDefault();
          if (btn.dataset.loading === 'true') {
            return;
          }

          togglePaymentButtonLoading(true);

          try {
            const response = await $wire.checkValidation();
            if (!response) {
              togglePaymentButtonLoading(false);
              return;
            }

            if (response.action === 'create') {
              try {
                const { error, setupIntent } = await stripe.confirmSetup({
                  elements,
                  redirect: 'if_required',
                });

                if (error) {
                  togglePaymentButtonLoading(false);
                  redirectToPaymentError(error.code || null, error.decline_code || null);
                  return;
                }

                $wire.dispatch('makeSubscription', { pm_id: setupIntent.payment_method });
              } catch (setupError) {
                console.error('Stripe setup confirmation failed', setupError);
                togglePaymentButtonLoading(false);
                redirectToPaymentError(setupError?.code || null, setupError?.decline_code || null);
              }
            } else {
              $wire.dispatch('makeSubscription', { pm_id: response.action });
            }
          } catch (validationError) {
            console.error('Validation error', validationError);
            togglePaymentButtonLoading(false);
          }
        });
      }

      $wire.on('requires-action', async (data) => {
        const params = data[0];
        const { error, paymentIntent } = await stripe.confirmCardPayment(params.clientSecret, {
          payment_method: params.paymentMethod,
        });

        const result = error ? 'error' : 'success';
        const pid = error ? error.paymentIntent.id : paymentIntent.id;
        $wire.paymentResult(result, pid);
      });
      
      $('.text-area-gift').on('input', function(evt) {
          $('.text-area-gift-counter').find('.text-area-counter').html(evt.target.value.length);
      });

      $('.promocode-input').on('input', function(evt) {
          $(this).val(evt.target.value.replace(' ', '').toUpperCase());
      });

      $('.promocode-input').on('change', function(evt) {
          $(this).val(evt.target.value.replace(' ', '').toUpperCase());
      });

      $('.apply').on('click', function(evt) {
          evt.preventDefault();
      });

      $('.is-gift-button').on('click', function() {
          const input = $('.is-gift');
          const val = (Number(input.val()) === 0) ? true : false;
      });
  </script>
@endscript
