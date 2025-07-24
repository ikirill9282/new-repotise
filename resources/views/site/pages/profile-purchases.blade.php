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
                    <p>Items <span> ({{ $user->orders->count() }}) </span></p>
                </div>
            </div>
            @if($user->orders->isNotEmpty())
            <div class="purch__items-head">
                <div class="col">Date</div>
                <div class="col">Order</div>
                <div class="col">Product</div>
                <div class="col">Actions</div>
                <div class="col">Price</div>
            </div>
            <div class="purch__items">
                @foreach($user->orders->sortByDesc('id') as $order)
                  @foreach($order->products as $product)
                    {{-- @dump($product) --}}
                    <div class="purch__item">
                        <div class="date">
                            <span>{{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d.m.Y') }}</span>
                        </div>
                        <div class="order">
                            <span>
                                #{{ $order->id }}
                            </span>
                        </div>
                        <div class="name">
                            <div class="img !w-16 !h-16 !max-w-none">
                                <img class="object-cover" src="{{ $product->preview->image }}" alt="Product '{{ $product->title }}' preview">
                            </div>
                            <p>
                                {{ $product->title }}
                            </p>
                        </div>
                        <div class="actions">
                            <div class="col">
                                @if($order->status_id == 1)
                                  <a class="black" href="{{ url("/profile/checkout?order=" . \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $order->id])) }}">
                                      Confirm Payment
                                  </a>
                                @else
                                  <a class="orange" href="#">
                                      View & Download
                                  </a>
                                @endif
                            </div>
                            <div class="col">
                                @if($user->canWriteComment($product))
                                  <a class="black" href="{{ $product->makeUrl() }}">Leave Review</a>
                                @endif
                                <a class="gray" href="#">Refund</a>
                            </div>
                        </div>
                        <div class="price">
                            <span>
                                {{ $product->pivot->price }}$
                            </span>
                        </div>
                    </div>
                  @endforeach
                @endforeach
            </div>
            @else
              No purchases yet.
            @endif
        </div>
    </x-profile.wrap>
@endsection
