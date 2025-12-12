<div>
  @if($orders->isNotEmpty())
    <div class="relative overflow-x-hidden max-w-full">

        <table class="table !mb-0 w-full">
          <thead>
            <tr class="">
              <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Date</th>
              <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Order</th>
              <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Product</th>
              <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Actions</th>
              <th class="text-nowrap font-normal !border-none !pb-4 !bg-light">Price</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
              @foreach ($order->order_products as $order_product)
                @php
                  $encryptedOrderProductId = \Illuminate\Support\Facades\Crypt::encryptString((string) $order_product->id);
                  $encryptedOrderId = \Illuminate\Support\Facades\Crypt::encryptString((string) $order->id);
                  $refundRequest = $order_product->refundRequest;
                @endphp
                <tr class="">
                  <td class="bg-clip-content !px-0 !text-gray !border-light !rounded-tl-2xl !rounded-bl-2xl">
                    <div class="!p-3 rounded-tl-lg rounded-bl-lg ">{{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d.m.Y') }}</div>
                  </td>
                  <td class="bg-clip-content !px-0 !text-gray !border-light">
                    <div class="!p-3 ">#{{ $order->id }}</div>
                  </td>
                  <td class="bg-clip-content !px-0 !text-gray !border-light">
                    <div class="!p-3 flex justify-start items-start gap-3 group ">
                      <div class="w-20 h-24 rounded overflow-hidden shrink-0">
                        <img class="w-full h-full object-cover" src="{{ $order_product->product->preview->image }}" alt="Image">
                      </div>
                      <div class="flex flex-col gap-1">
                        <x-link 
                          href="{{ $order_product->product->makeUrl() }}" 
                          class="!border-0 group-has-[a]:!text-black break-words"
                          >
                            {{ $order_product->product->title }}
                          </x-link>
                        @if($order->is_gift_order && $order->buyer_user_id === $user->id)
                          <span class="text-xs">🎁 Gift purchase</span>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td class="bg-clip-content !px-0 !border-light">
                    <div class="!p-3 flex items-start justify-start gap-4 group flex-wrap">
                      @if($order->status_id !== \App\Enums\Order::NEW)
                        @php
                          $isGiftBuyer = $order->is_gift_order && $order->buyer_user_id === $user->id;
                          $isGiftRecipient = $order->is_gift_order && $order->buyer_user_id !== $user->id;
                          $gift = $order->gift;
                        @endphp
                        
                        @if($isGiftBuyer && $gift)
                          {{-- Для покупателя подарка --}}
                          <div class="flex flex-col items-start justify-start gap-2 w-full">
                            @if($gift->status === \App\Models\Gift::STATUS_CREATED)
                              <div class="text-gray-400 text-sm">Gift sent to {{ $gift->recipient_email }}</div>
                              <div class="flex flex-col items-start gap-1">
                                @php
                                  $encryptedGiftId = \Illuminate\Support\Facades\Crypt::encryptString((string) $gift->id);
                                @endphp
                                <x-link 
                                  wire:click.prevent="openEditRecipientModal('{{ $encryptedGiftId }}')" 
                                  class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black text-sm"
                                >
                                  Edit recipient email
                                </x-link>
                                <x-link 
                                  wire:click.prevent="openResendGiftModal('{{ $encryptedGiftId }}')" 
                                  class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black text-sm"
                                >
                                  Resend gift email
                                </x-link>
                                @php
                                  // Рефанд доступен только если gift.status = created и по товару разрешён рефанд
                                  $canRefundGift = !$order_product->refunded && !$refundRequest && $gift->status === \App\Models\Gift::STATUS_CREATED;
                                @endphp
                                @if($canRefundGift)
                                  <x-link 
                                    wire:click.prevent="openRefundModal('{{ $encryptedOrderProductId }}', '{{ $encryptedOrderId }}')" 
                                    class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black text-sm"
                                  >
                                    Request refund
                                  </x-link>
                                @endif
                              </div>
                            @elseif($gift->status === \App\Models\Gift::STATUS_CLAIMED)
                              <div class="text-gray-400 text-sm">Gift claimed on {{ $gift->claimed_at->format('d.m.Y') }}</div>
                            @endif
                          </div>
                        @else
                          {{-- Обычная покупка или получатель подарка --}}
                          <div class="flex group">
                            @if(!$order_product->refunded)
                              <x-link 
                                class="group-has-[a]:!text-active" 
                                wire:click.prevent="openProductModal('{{ $encryptedOrderProductId }}', '{{ $encryptedOrderId }}')"
                              >
                                View & Download
                              </x-link>
                            @else
                              <div class="flex group opacity-0">View &amp; Download</div>
                            @endif
                          </div>
                          <div class="flex flex-col items-start justify-start gap-2">
                            @php
                              $canLeaveReview = $user->canWriteReview($order_product->product)
                                && !($refundRequest && $refundRequest->status === 'approved');
                            @endphp
                            @if ($canLeaveReview)
                              <x-link 
                                href="{{ $order_product->product->makeUrl() }}#review" 
                                class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black"
                              >
                                Leave Review
                              </x-link>
                            @endif
                            @if($refundRequest)
                              @php
                                $status = $refundRequest->status ?? 'pending';
                                $statusLabel = match ($status) {
                                  'approved' => 'Returned',
                                  'rejected' => 'Return Denied',
                                  'pending' => 'Return Requested',
                                  default => ucfirst(str_replace('_', ' ', (string) $status)),
                                };
                                $reason = $refundRequest->reason
                                  ? ucfirst(str_replace('_', ' ', $refundRequest->reason))
                                  : null;
                                $details = trim(strip_tags($refundRequest->details ?? ''));
                              @endphp
                              <div class="text-left max-w-xs">
                                <span class="text-gray-400 cursor-not-allowed block">{{ $statusLabel }}</span>
                                @if($reason)
                                  <span class="text-xs text-gray-400 block">{{ $reason }}</span>
                                @endif
                                @if($details)
                                  <span class="text-xs text-gray-400 block">{{ $details }}</span>
                                @endif
                              </div>
                            @elseif(!$isGiftRecipient)
                              {{-- Refund показываем только если это не получатель подарка --}}
                              <x-link 
                                wire:click.prevent="openRefundModal('{{ $encryptedOrderProductId }}', '{{ $encryptedOrderId }}')" 
                                class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black"
                              >
                                Refund
                              </x-link>
                            @endif
                          </div>
                        @endif
                      @else
                        <div class="flex group opacity-0">View & Download</div>
                        <div class="flex flex-col items-start justify-start gap-2">
                          <x-link wire:click.prevent="moveCheckout('{{ \Illuminate\Support\Facades\Crypt::encrypt($order->id) }}')" class="group-has-[a]:hover:!text-black group-has-[a]:hover:!border-black">Complete payment</x-link>
                        </div>
                      @endif
                    </div>
                  </td>
                  <td class="bg-clip-content !px-0 !border-light !rounded-tr-2xl !rounded-br-2xl">
                    <div class="!p-3">{{ currency($order_product->getPrice()) }}</div>
                  </td>
                </tr>
              @endforeach
            @endforeach
          </tbody>
          <tfoot></tfoot>
        </table>

    </div>

  @else

    <div class="text-center !py-10">
      You haven't placed any orders yet. <x-link href="{{ route('products') }}">Discover your advanture now!</x-link>
    </div>

  @endif

</div>
