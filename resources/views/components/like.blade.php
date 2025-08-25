@props([
  'type' => 'article',
  'id' => null,
  'count' => 0,
])

@php
  $hash_id = \App\Helpers\CustomEncrypt::generateUrlHash([$id]);
@endphp

<a 
    href="/feedback/likes"
    class="feedback_button flex items-center justify-start gap-0.5 sm:gap-1
      !text-gray
      {{ auth()->check() ? '' : 'open_auth' }} 
      {{ is_liked($type, $id) ? 'liked' : '' }}
      "
    data-item="{{ hash_like($type, $id) }}" data-id="{{ $hash_id }}">
    @if($slot->isNotEmpty())
      {{ $slot }}
    @else
      <span>
          @include('icons.like')
      </span>
      <span>
          <span>Like</span>
          <span class="!text-black" data-counter="{{ $hash_id }}">
              {{ $count }}
          </span>
      </span>
    @endif
</a>
