<div class="flex flex-col justify-start items-stretch gap-2">
  @if ($owner)
    <div class="flex justify-between items-center mb-2">
      <p class="text-sm text-gray">Control which social links are visible to visitors.</p>
      <div class="flex items-center gap-2 text-xs">
        <button 
          type="button"
          wire:click="toggleAll(true)"
          class="text-primary hover:underline disabled:opacity-50 disabled:pointer-events-none"
          @disabled(empty(array_filter($social)))
        >
          Show all
        </button>
        <span class="text-gray/60 select-none">•</span>
        <button 
          type="button"
          wire:click="toggleAll(false)"
          class="text-primary hover:underline disabled:opacity-50 disabled:pointer-events-none"
          @disabled(empty(array_filter($social)))
        >
          Hide all
        </button>
      </div>
    </div>

    @foreach ($icons as $key => $icon)
      <div class="p-2.5 rounded-lg bg-light flex justify-between items-center">
        <div class="creatorPage__aside-connectSocials-item-author-wrapper flex items-center gap-3">
          <img src="{{ $icon }}" alt="{{ $labels[$key] ?? ucfirst($key) }}" class="w-8 h-8 object-contain" />
          <div class="flex flex-col">
            <p class="creatorPage__aside-connectSocials-item-socialName font-semibold capitalize">
              {{ $labels[$key] ?? ucfirst($key) }}
            </p>
            @if(!empty($social[$key]))
              <a 
                href="{{ $social[$key] }}" 
                target="_blank" 
                rel="noopener" 
                class="text-xs text-primary underline decoration-dotted hover:decoration-solid"
              >
                {{ $social[$key] }}
              </a>
            @else
              <span class="text-xs text-gray">Link not set</span>
            @endif
          </div>
        </div>

        <label for="toggle-{{ $key }}" class="leading-0 hover:cursor-pointer {{ empty($social[$key]) ? 'opacity-40 pointer-events-none' : '' }}">
          <input 
            type="checkbox" 
            id="toggle-{{ $key }}" 
            class="creatorPage__aside-connectSocials-item-checkbox"
            @checked($visibility[$key] ?? false)
            wire:change="setVisibility('{{ $key }}', $event.target.checked)"
            @disabled(empty($social[$key]))
          />
          <span class="toggle-switch" wire:loading.class="opacity-70 pointer-events-none"></span>
        </label>
      </div>
    @endforeach
  @else
    @if (count($visibleSocials))
      <div class="flex justify-start items-center gap-2 p-2 bg-light rounded flex-wrap">
        @foreach ($visibleSocials as $key => $link)
          <a 
            href="{{ $link }}" 
            target="_blank" 
            rel="noopener" 
            class="w-10 h-10 flex items-center justify-center rounded-full transition hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
            title="{{ $labels[$key] ?? ucfirst($key) }}"
          >
            <img class="w-full h-full object-contain" src="{{ $icons[$key] }}" alt="{{ $labels[$key] ?? ucfirst($key) }}">
          </a>
        @endforeach
      </div>
    @else
      <div class="text-sm text-gray italic">No social links available.</div>
    @endif
  @endif
</div>

