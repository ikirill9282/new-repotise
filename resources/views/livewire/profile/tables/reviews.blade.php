<div id="reviews-summary">
    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
      <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
        <div class="mr-auto">Recent Reviews</div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Total Reviews:</div>
            <div class="text-nowrap">{{ number_format($summary['total_reviews']) }}</div>
          </div>
          <div class="flex justify-start items-start gap-2">
            <div class="text-gray">Average Rating:</div>
            <div class="text-nowrap">
              {{ $summary['average_rating'] > 0 ? number_format($summary['average_rating'], 2) : '—' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($rows->isEmpty())
      <div class="text-lg text-center text-gray">You haven't received any reviews yet.</div>
    @else
      <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
        <table class="table">
          <thead>
            <tr class="">
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Reviewer</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Rating</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Review</th>
              <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $review)
              @php
                $author = $review->author;
                $product = $review->product;
                $date = $review->created_at;
                $formattedDate = $date
                  ? $date->copy()->timezone(config('app.timezone'))->format('m.d.Y')
                  : '—';
                $reviewText = $review->text ? \Illuminate\Support\Str::limit(strip_tags($review->text), 100) : null;
                $replyUrl = $product ? $product->makeUrl().'#review' : null;
              @endphp
              <tr>
                <td class="!border-b-gray/15 !py-4 text-nowrap">
                  {{ $author?->username ?? $author?->name ?? '—' }}
                </td>
                <td class="!border-b-gray/15 !py-4 text-nowrap">
                  {{ $product?->title ?? 'Product removed' }}
                </td>
                <td class="!border-b-gray/15 !py-4">
                  <div class="flex items-center gap-1">
                    <x-stars :active="$review->rating" />
                    <span class="text-sm text-gray">({{ $review->rating }})</span>
                  </div>
                </td>
                <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">
                  {{ $formattedDate }}
                </td>
                <td class="!border-b-gray/15 !py-4 min-w-2xs">
                  @if($reviewText)
                    <span title="{{ strip_tags($review->text) }}">{{ $reviewText }}</span>
                  @else
                    <span class="text-gray">—</span>
                  @endif
                </td>
                <td class="!border-b-gray/15 !py-4">
                  @if($replyUrl)
                    <x-link :href="$replyUrl">Reply</x-link>
                  @else
                    <span class="text-gray">—</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot></tfoot>
        </table>
      </div>

    
    @endif

    <div class="text-right mt-4">
      <x-btn href="{{ route('profile.reviews') }}#reviews" outlined class="!border-active hover:!border-second !w-auto !px-12">
        View All Reviews
      </x-btn>
    </div>
</div>
