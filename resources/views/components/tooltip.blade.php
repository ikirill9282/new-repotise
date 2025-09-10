@props(['message' => '', 'class' => ''])

<div class="tooltip !absolute top-[50%] right-0 translate-y-[-50%] z-20 !opacity-100 {{ $class }}">
  @if(!$slot->isEmpty())
    {{ $slot }}
  @else
    @include('icons.shield')
  @endif
  <div class="tooltip-text bg-second 
      after:!border-s-transparent after:!border-t-second 
      after:!border-r-transparent after:!border-b-transparent
      "
    >
    {{ $message }}</div>
</div>