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
              $product = $sub->productModel;
              $latestPayment = $sub->latestPayment;
              $paymentIntent = $sub->latestPaymentIntent;
              $paymentMethodDetails = $paymentIntent?->charges->data[0]->payment_method_details->card ?? null;
              $encryptedSubscriptionId = \Illuminate\Support\Facades\Crypt::encryptString((string) $sub->id);
            @endphp
            @continue(!$product)
            <tr>
              <td class="!border-none bg-clip-content !px-0 !text-gray !rounded-tl-2xl !rounded-bl-2xl">
                <div class="!p-3 flex justify-start items-start gap-3 group">
                  <div class="w-20 h-24 rounded overflow-hidden shrink-0">
                    <img class="w-full h-full object-cover" src="{{ $product->preview->image ?? asset('assets/img/checked.png') }}" alt="Image">
                  </div>
                  <x-link class="!border-0 group-has-[a]:!text-black text-nowrap">{{ $product->title }}</x-link>
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 !text-gray">
                <div class="!p-3 ">
                  {{ optional($sub->nextBillingDate)->format('d.m.Y') ?? '—' }}
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 !text-gray">
                <div class="!p-3 ">
                  @if($paymentMethodDetails)
                    {{ ucfirst($paymentMethodDetails->brand) }} **** {{ $paymentMethodDetails->last4 }}
                  @else
                    —
                  @endif
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 ">
                <div class="!p-3 ">
                  {{ currency($latestPayment->amount ?? ($paymentIntent?->amount / 100 ?? 0)) }}/{{ $sub->periodLabel ?? '—' }}
                </div>
              </td>
              <td class="!border-none bg-clip-content !px-0 text-nowrap !rounded-tr-2xl !rounded-br-2xl">
                <div class="!p-3 flex flex-col gap-2 items-start text-left">
                  <x-link 
                    wire:click.prevent="openSubscriptionModal('{{ $encryptedSubscriptionId }}')" 
                    class="group-has-[a]:!text-active"
                  >
                    View & Download
                  </x-link>
                  <x-link
                    wire:click.prevent="openCancelModal('{{ $encryptedSubscriptionId }}')"
                    class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black"
                  >
                    Cancel Subscription
                  </x-link>
                  <span class="uppercase text-xs tracking-wide text-gray">{{ $sub->stripe_status }}</span>
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
