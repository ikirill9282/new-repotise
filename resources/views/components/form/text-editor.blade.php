@props([
  'label' => '',
  'placeholder' => '',
  'id' => 'id'.uniqid(),
  'image' => true,
  'tooltip' => false,
  'tooltipText' => null,
])
        
<div 
  class="relative !text-sm sm:!text-base text-editor"
  >
  <textarea {{ $attributes }} id="{{ $id }}" class="!w-0 !h-0 !opacity-0 absolute"></textarea>
  <div class="!mb-2 text-gray">{{ $label }}</div>
  <div 
    class="quill-editor !bg-light !border-none !rounded-lg min-h-36 grid grid-cols-1
      !pr-4 !text-sm sm:!text-base max-h-[600px] overflow-auto scrollbar-custom" 
    data-placeholder="{{ $placeholder }}"
    data-model="{{ $id }}"
    data-image="{{ $image ? 'true' : 'false' }}"
  >
  </div>
  @if($tooltip && filled($tooltipText))
    <x-tooltip class="!right-3 !top-32 xs:!top-25" :message="$tooltipText"></x-tooltip>
  @endif

  @if ($attributes->get('name'))
    @error($attributes->get('name'))
      <div class="!mt-2 text-red-500">{{ $message }}</div>
    @enderror
  @endif
</div>
<style>
  /* Стили для списков в Quill редакторе */
  .quill-editor .ql-editor ul,
  .quill-editor .ql-editor ol {
    list-style-position: outside !important;
    padding-left: 30px !important;
    margin: 15px 0 !important;
  }
  .quill-editor .ql-editor ul {
    list-style-type: disc !important;
    list-style: disc outside !important;
  }
  .quill-editor .ql-editor ol {
    list-style-type: decimal !important;
    list-style: decimal outside !important;
  }
  .quill-editor .ql-editor li {
    display: list-item !important;
    list-style-position: outside !important;
    margin: 8px 0 !important;
    padding-left: 5px !important;
    line-height: 1.6 !important;
  }
  .quill-editor .ql-editor ul ul {
    list-style-type: circle !important;
    list-style: circle outside !important;
  }
  .quill-editor .ql-editor ul ul ul {
    list-style-type: square !important;
    list-style: square outside !important;
  }
</style>