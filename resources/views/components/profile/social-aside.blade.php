@props([
  'owner' => false,
  'social' => [],
])
@php
  $icons = \App\Models\UserOptions::getSocialIcons();
@endphp

<div class="flex flex-col justify-start items-stretch gap-2">
    @if($owner)
      @foreach(array_filter($social, fn($soc) => !is_null($soc)) as $key => $social_link)
        <div class="p-2.5 rounded-lg bg-light flex justify-between items-center">
            <div class="creatorPage__aside-connectSocials-item-author-wrapper">
                <img src="{{ $icons[$key] }}" alt="{{ ucfirst($key) }}" />
                <p class="creatorPage__aside-connectSocials-item-socialName">{{ ucfirst($key) }}</p>
            </div>
            <label for="{{ $key }}" class="leading-0 hover:cursor-pointer">
                <input type="checkbox" id="{{ $key }}" class="creatorPage__aside-connectSocials-item-checkbox" />
                <span class="toggle-switch"></span>
            </label>
        </div>
      @endforeach
    @else
      <div class="flex justify-start items-center !gap-2 !p-2 bg-light rounded">
        @foreach ($icons as $key => $icon)
          <a href="#" class="w-full max-w-10">
            <img class="w-full" src="{{ $icon }}" alt="{{ ucfirst($key) }}">
          </a>
        @endforeach
      </div>
    @endif
</div>
