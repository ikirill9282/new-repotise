@props([
  'items' => [],
  'parent' => '#accordion',
])

<div class="accordion" id="accordion">
    @foreach($items as $item)
      @php
        $key = uniqid();   
      @endphp
      <div class="accordion-item group !rounded-none !border-x-0 !border-t-0 !border-b-1 border-gray has-[.show]:!border-active">
          <div class="accordion-header">
              <button
                  class="accordion-button collapsed !px-0 sm:!text-lg !shadow-none !bg-inherit !text-gray 
                   group-hover:!text-dark group-has-[.show]:!text-dark"
                  type="button" 
                  data-bs-toggle="collapse" 
                  data-bs-target="#collapse-{{ $key }}" 
                  aria-controls="collapse-{{ $key }}"
                  aria-expanded="true"
                >
                  <span class="inline-block !pr-2.5">{{ $item['title'] ?? '' }}</span>
              </button>
          </div>
          <div id="collapse-{{ $key }}" class="accordion-collapse collapse" data-bs-parent="{{ $parent }}">
            <p class="py-3">{{ $item['text'] ?? '' }}</p>
          </div>
      </div>
    @endforeach
</div>
