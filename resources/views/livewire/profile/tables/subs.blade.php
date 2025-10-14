<div>

    @if($subs?->isNotEmpty())
    <div class="relative overflow-x-scroll max-w-full scrollbar-custom">

      <table class="table !mb-0">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Product</th>
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Next Billing Date</th>
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Payment Method</th>
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Amount</th>
            <th class="text-nowrap font-normal !border-none !bg-transparent !border-b-gray/15 !pb-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($subs as $sub)
            @php
              $product = $sub->order_products->first();
              $paymentMethodId = $sub->getTransaction()?->payment_method;
              $paymentMethod = $paymentMethodId ? $sub->user->findPaymentMethod($paymentMethodId) : null;
            @endphp
            <tr>
              <td class="!border-none bg-clip-content !px-0 !text-gray !rounded-tl-2xl !rounded-bl-2xl">
                <div class="!p-3 flex justify-start items-start gap-3 group">
                  <div class="w-20 h-24 rounded overflow-hidden shrink-0">
                    <img class="w-full h-full object-cover" src="{{ $product->product->preview->image }}" alt="Image">
                  </div>
                  <x-link class="!border-0 group-has-[a]:!text-black text-nowrap">{{ $product->product->title }}</x-link>
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 !text-gray">
                <div class="!p-3 ">
                  @if($paymentMethod)
                    {{ \Illuminate\Support\Carbon::parse($sub->created_at)->format('d.m.Y') }}
                  @endif
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 !text-gray">
                <div class="!p-3 ">
                  @if($paymentMethod && $paymentMethod->type == 'card')
                    {{ ucfirst($paymentMethod->card->brand) }} **** {{ $paymentMethod->card->last4 }}
                  @endif
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 ">
                <div class="!p-3 ">
                  {{ currency($product->total) }}/{{ $sub->sub_period }}
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 text-nowrap !rounded-tr-2xl !rounded-br-2xl">
                <div class="!p-3 ">
                  @if($sub->status_id !== \App\Enums\Order::NEW)
                    @if(!$sub->user->subscription($sub->getSubscriptionType())?->asStripeSubscription()->cancel_at_period_end)
                      <x-link 
                        wire:click.prevent="$dispatch('openModal', { modalName: 'cancelsub', args: { order_id: '{{ \Illuminate\Support\Facades\Crypt::encrypt($sub->id) }}' } })" 
                        class="group-has-[a]:hover:!text-black"
                      >
                        Cancel Subscription
                      </x-link>
                    @endif
                  @else
                    <div class="flex flex-col !gap-3 items-start">
                      <x-link 
                          wire:click.prevent="completePayment('{{ \Illuminate\Support\Facades\Crypt::encrypt($sub->id) }}')" 
                          class="group-has-[a]:hover:!text-black"
                        >
                          Complete Payment
                        </x-link>
                      <x-link 
                          wire:click.prevent="$dispatch('openModal', {modalName: 'delete-subscription', args: { order_id: '{{ \Illuminate\Support\Facades\Crypt::encrypt($sub->id) }}' }})"
                          class="group-has-[a]:hover:!text-black"
                        >
                          Delete Subscription
                        </x-link>
                    </div>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
    @else

      <div class="text-center !py-10">
        You haven't placed any subscriptions yet. <x-link href="{{ route('products') }}">Discover your advanture now!</x-link>
      </div>
    @endif
</div>
