@extends('layouts.site')

@section('content')
    <x-profile.wrap>
        <div class="the-content__content">
            @if(!$user->verified)
              <x-profile.verify />
            @endif
            <div class="purch__tabs">
                <div class="btns">
                    <button class="active purch-btn">Products</button>
                    <button class="purch-btn">Subscriptions</button>
                </div>
                <div class="count">
                    <p>Items <span> ({{ $products->total() }}) </span></p>
                </div>
            </div>
            @if($products->isNotEmpty())
            <div class="purch__items-head">
                <div class="col">Date</div>
                <div class="col">Order</div>
                <div class="col">Product</div>
                <div class="col">Actions</div>
                <div class="col">Price</div>
            </div>
            <div class="purch__items mb-4">
              @foreach($products as $op)
                {{-- @dump($product) --}}
                <div class="purch__item">
                    <div class="date">
                        <span>{{ \Illuminate\Support\Carbon::parse($op->order->created_at)->format('d.m.Y') }}</span>
                    </div>
                    <div class="order">
                        <span>
                            #{{ $op->order->id }}
                        </span>
                    </div>
                    <div class="name">
                        <div class="img !w-16 !h-16 !max-w-none">
                            <img class="object-cover" src="{{ $op->product->preview->image }}" alt="Product '{{ $op->product->title }}' preview">
                        </div>
                        <p>
                            <a href="{{ $op->product->makeUrl() }}" class="link-black">{{ $op->product->title }} x {{ $op->count }}</a>
                        </p>
                    </div>
                    <div class="actions">
                          <div class="col">
                              @if($op->order->status_id == 1)
                                <a class="black" href="{{ url("/profile/checkout?order=" . \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $op->order_id])) }}">
                                    Await Payment
                                </a>
                              @else

                                @if($op->order->gift && $op->order->recipient !== $user->email)
                                  <span class="gray">Gift</span>
                                @else
                                <a class="orange" href="#">
                                    View & Download
                                </a>
                                @endif
                              @endif
                          </div>

                          <div class="col">
                              @if ($op->order->status_id >= 2)
                                
                                @if($user->canWriteComment($op->product))
                                  <a class="black" href="{{ $op->product->makeUrl() }}">Leave Review</a>
                                @endif

                                @if ($op->order->gift && !$op->order->recipient === $user->email)
                                  <a class="gray" href="#">Refund</a>
                                @endif
                              @endif
                          </div>
                    </div>
                    <div class="price">
                        @if($op->order->gift)
                          @if($op->order->recipient == $user->email)
                            <span class="gray">Gift</span>
                          @else
                            <span>
                                {{ $op->order->cost == 0 ? 0 : $op->getTotal() }}$
                            </span>
                          @endif
                        @else
                          <span>
                              {{ $op->order->cost == 0 ? 0 : $op->getTotal() }}$
                          </span>
                        @endif
                    </div>
                </div>
              @endforeach
            </div>

            @include('site.components.paginator', ['paginator' => $products])
            @else
              No purchases yet.
            @endif
        </div>
    </x-profile.wrap>
@endsection
