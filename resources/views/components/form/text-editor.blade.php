@props([
  'label' => '',
  'placeholder' => '',
  'id' => 'id'.uniqid(),
])
        
<div class="relative !text-sm sm:!text-base text-editor">
  <textarea {{ $attributes }} id="{{ $id }}" class="!w-0 !h-0 !opacity-0 absolute"></textarea>
  <div class="!mb-2 text-gray">{{ $label }}</div>
  <div 
    class="quill-editor !bg-light !border-none !rounded-lg min-h-36 grid grid-cols-1 !pr-4 !text-sm sm:!text-base" 
    data-placeholder="{{ $placeholder }}"
    data-model="{{ $id }}"
  >
  </div>
  <x-tooltip class="!right-3 !top-32 xs:!top-25" message="tooltip"></x-tooltip>
</div>