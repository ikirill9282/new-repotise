@php
$products = \App\Models\Product::getTrendingProducts(includes: $variables->get('product_ids')?->value ?? []);
@endphp

<section class="popular_products">
  <div class="container !mx-auto">
      <div class="about_block">
          @include('site.components.heading')
          <div class="products_item !items-stretch !grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
              @foreach($products as $product)
                @include('site.components.cards.product', [
                  'model' => $product,
                  'variables' => $variables,
                ])
              @endforeach
          </div>
          <a href="{{ print_var('more_link', $variables) }}" class="look_more">{{ print_var('more_text', $variables) }}</a>
      </div>
  </div>
</section>