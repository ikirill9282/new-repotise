@php
    use Illuminate\Support\Facades\Crypt;

    $files = $product?->files ?? collect();
    $links = $product?->links ?? collect();
    $subscriptionHash = $subscription ? Crypt::encryptString((string) $subscription->id) : null;
@endphp

<div class="w-full">
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">
    {{ $product?->title ?? 'Your Subscription' }}
  </div>

  @if(!empty($errorMessage))
    <div class="p-4 bg-red-50 text-red-500 rounded-lg">{{ $errorMessage }}</div>
  @elseif(!$subscription || !$product)
    <div class="p-4 bg-light rounded-lg text-gray">
      We couldn’t find any digital assets for this subscription yet.
    </div>
  @else
    @if(!empty($product->text))
      <div class="mb-6">
        <div class="text-lg font-semibold mb-3">Description from Creator:</div>
        <div class="prose max-w-none text-gray-700">{!! $product->getText() !!}</div>
      </div>
    @endif

    <div class="mb-6">
      <div class="text-lg font-semibold mb-3">Downloadable files</div>

      @if($files->isNotEmpty())
        <div class="flex flex-col gap-3">
          @foreach($files as $file)
            @php
                $fileHash = Crypt::encryptString((string) $file->id);
            @endphp
            <div class="flex flex-col sm:flex-row sm:items-start gap-3 bg-light rounded-lg p-4">
              <div class="flex items-start gap-3">
                <span class="text-active shrink-0">@include('icons.docs')</span>
                <div>
                  <div class="font-medium">{{ $file->name ?? 'File '.$loop->iteration }}</div>
                  <div class="text-xs text-gray">{{ number_format($file->size ?? 0, 2) }} MB</div>
                </div>
              </div>
              <div class="sm:ml-auto flex flex-col gap-2 w-full sm:w-auto">
                @if(!empty($file->description))
                  <div class="prose max-w-none text-sm text-gray">{!! $file->description !!}</div>
                @endif
                <x-link 
                  href="{{ route('subscriptions.files.download', ['subscription' => $subscriptionHash, 'file' => $fileHash]) }}"
                  class="inline-flex items-center justify-center !border-[#FC7361] !text-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] !py-2 !px-4 !rounded transition text-sm"
                >
                  Download
                </x-link>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="p-4 bg-white rounded-lg text-gray">
          The creator hasn’t attached downloadable files yet.
        </div>
      @endif
    </div>

    <div class="mb-6">
      <div class="text-lg font-semibold mb-3">Additional resources</div>

      @if($links->isNotEmpty())
        <div class="flex flex-col gap-3">
          @foreach($links as $link)
            <div class="bg-light rounded-lg p-4">
              <div class="font-medium mb-1">{{ $link->name ?? $link->link }}</div>
              <a 
                href="{{ $link->link }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-[#FC7361] hover:text-[#484134] transition text-sm"
              >
                <span>Open link</span>
                @include('icons.arrow_right')
              </a>
            </div>
          @endforeach
        </div>
      @else
        <div class="p-4 bg-white rounded-lg text-gray">
          No external resources were provided for this product.
        </div>
      @endif
    </div>

    <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
      <x-btn class="!text-sm sm:!text-base w-auto m-0" wire:click.prevent="$dispatch('closeModal')" outlined>Close</x-btn>
      @if($product)
        <x-link 
          href="{{ $product->makeUrl() }}" 
          target="_blank" 
          class="!text-sm sm:!text-base w-auto m-0 !border-[#FC7361] !text-[#FC7361] hover:!border-[#484134] hover:!text-[#484134] !py-2.5 !px-6 !rounded transition"
        >
          View Product Page
        </x-link>
      @endif
    </div>
  @endif
</div>
