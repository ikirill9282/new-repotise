<div id="reviews">
  <div class="flex justify-between items-center mb-4">
    <div class="font-bold text-2xl">Reviews</div>
    <div class="block">
      <label class="text-gray" for="sorting-reviews">Sort By:</label>
      <select
        wire:model="sorting"
        id="sorting-reviews"
        class="outline-0 pr-1 hover:cursor-pointer"
      >
        <option value="newest">Newest First</option>
        <option value="oldest">Oldest First</option>
        <option value="rating_high">Highest Rating</option>
        <option value="rating_low">Lowest Rating</option>
      </select>
    </div>
  </div>

  @if($reviews->isEmpty())
    <div class="text-center text-gray py-6">
      You haven't received any reviews yet.
    </div>
  @else
    <div class="overflow-x-scroll scrollbar-custom mb-4">
      <table class="table text-sm md:text-base">
        <thead>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </thead>
        <tbody>
          @foreach($reviews as $review)
            @php
              $author = $review->author;
              $product = $review->product;
              $avatar = $author?->avatar ?? asset('assets/img/avatar.svg');
              $preview = $product?->preview?->image;
              $createdAt = $review->created_at?->copy()->timezone(config('app.timezone'));
            @endphp
            <tr>
              <td class="!border-b-gray/15 !py-4 text-nowrap">
                <div class="flex justify-start items-start gap-2">
                  <div class="!w-12 !h-12 rounded-full overflow-hidden shrink-0 bg-light">
                    <img class="!w-full !h-full object-cover" src="{{ $avatar }}" alt="{{ $author?->username ?? 'User' }}">
                  </div>
                  <div class="flex flex-col">
                    <p>{{ $author?->username ?? $author?->name ?? 'Anonymous' }}</p>
                    <p class="font-thin text-gray text-sm">
                      <span>{{ $createdAt?->format('m.d.Y') ?? '—' }}</span>
                      <span>{{ $createdAt?->format('H:i') ?? '' }}</span>
                    </p>
                    <p class="min-w-xs max-w-lg whitespace-normal">
                      {{ trim(strip_tags($review->text)) ?: 'No review text provided.' }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4 ">
                <div class="flex items-center justify-start gap-2">
                  <div class="!w-14 !h-22 rounded overflow-hidden shrink-0 bg-light">
                    @if($preview)
                      <img class="w-full h-full object-cover" src="{{ $preview }}" alt="{{ $product?->title ?? 'Product' }}">
                    @else
                      <div class="w-full h-full flex items-center justify-center text-xs text-gray">
                        No Image
                      </div>
                    @endif
                  </div>
                  <div class="min-w-xs">
                    <p>{{ $product?->title ?? 'Product removed' }}</p>
                  </div>
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap align-middle ">
                <div class="flex justify-center items-center h-full gap-2">
                  <x-stars :active="(int) round($review->rating ?? 0)" />
                  <span>{{ number_format($review->rating ?? 0, 1) }}</span>
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4 align-middle">
                <div class="flex justify-center items-center h-full">
                  @if($product)
                    @php
                      $replyLabel = ($review->seller_reply_count ?? 0) > 0 ? 'View' : 'Reply';
                    @endphp
                    <x-link href="{{ $product->makeUrl() }}#review">{{ $replyLabel }}</x-link>
                  @else
                    <span class="text-gray">—</span>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot></tfoot>
      </table>
    </div>
    @if($hasMore)
      <div class="text-center">
        <x-link wire:click.prevent="loadMore">Show More</x-link>
      </div>
    @endif
  @endif
</div>
