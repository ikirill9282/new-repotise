@extends('layouts.site')

@section('content')
    <x-profile.wrap>
        <div class="the-content__content">
            @if(!$user->hasRole('creator'))
              <x-profile.begin-verify class="mb-4" />
            @endif

            @livewire('profile.tables', [
              'active' => isset($type) && $type == 'subscriptions' ? 'subs' : 'orders',
              'args' => [
                'user_id' => \Illuminate\Support\Facades\Crypt::encrypt($user->id),
              ],
              'tables' => [
                [
                  'name' => "orders",
                  'title' => "Products",
                  'href' => route('profile.purchases')
                ],
                [
                  'name' => 'subs',
                  'title' => "Subscriptions",
                  'href' => route('profile.purchases.subscriptions', ['type' => 'subscriptions'])
                ],
              ],
            ])
        </div>
    </x-profile.wrap>
@endsection

@push('js')
  <script>
    (function () {
      const params = new URLSearchParams(window.location.search);
      const orderProductId = params.get('open_order_product_id');
      const orderId = params.get('open_order_id');

      if (!orderProductId || !orderId) return;

      function tryOpen() {
        if (!window.Livewire || typeof window.Livewire.dispatch !== 'function') return false;
        window.Livewire.dispatch('openModal', { modalName: 'product', args: { order_product_id: orderProductId, order_id: orderId } });
        return true;
      }

      if (tryOpen()) return;

      // Livewire может загрузиться чуть позже на тяжелых страницах
      let attempts = 0;
      const timer = setInterval(() => {
        attempts++;
        if (tryOpen() || attempts >= 20) {
          clearInterval(timer);
        }
      }, 250);
    })();
  </script>
@endpush
