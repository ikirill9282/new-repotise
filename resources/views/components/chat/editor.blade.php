@props([
  'target' => null,
])

<div class="relative">
  <div class="rotate-90 hover:cursor-pointer editor_btn transition
            text-gray hover:text-active
            {{ auth()->check() ? '' : 'open_auth' }}
            " 
        data-target="editor-{{ $target }}"
      >
      @include('icons.options', ['width' => 40, 'height' => 40])
  </div>
  <div class="absolute top-0 left-0 z-20 translate-x-[-100%] h-0 overflow-hidden transition select-none" id="editor-{{ $target }}" data-model="{{ $target }}">
      <div class="list flex flex-col items-stretch justify-center text-center gap-1">
        <x-chat.editor-item>Report</x-chat.editor-item>

        @if(auth()->user()?->hasRole(['admin', 'super-admin']))
          <x-chat.editor-item>Edit</x-chat.editor-item>
        @endif
        
        @if(auth()->user()?->hasRole(['super-admin']))
          <x-chat.editor-item>Delete</x-chat.editor-item>
        @endif
      </div>
  </div>
</div>
