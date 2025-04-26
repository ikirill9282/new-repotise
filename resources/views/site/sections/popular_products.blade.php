@php
$products = $variables->get('product_ids')->value;
$products = \App\Models\Product::query()
  ->whereIn('id', $products)
  ->with('preview', 'location', 'categories', 'type')
  ->withCount(['reviews' => function($query) {
    $query->whereNull('parent_id');
  }])
  ->get();

while($products->count() < 10) {
  $products = $products->collect()->merge($products)->slice(0, 10);
}
@endphp

<section class="popular_products">
  <div class="container !mx-auto">
      <div class="about_block">
          @include('site.components.heading')
          <div class="products_item !grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
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