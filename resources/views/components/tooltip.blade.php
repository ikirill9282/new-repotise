@props(['message' => '', 'class' => ''])

@push('css')
  <style>
  .tooltip {
    position: relative;
    display: inline-block;
    cursor: pointer;
  }
  .tooltip .tooltip-text {
    visibility: hidden;
    width: max-content;
    max-width: 200px;
    background-color: #333;
    color: #fff;
    text-align: center;
    padding: 6px 8px;
    border-radius: 4px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
  }
  .tooltip .tooltip-text::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #333 transparent transparent transparent;
  }
  .tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
  }
</style>
@endpush

<div class="tooltip {{ $class }}">
  {{ $slot }}
  <div class="tooltip-text">{{ $message }}</div>
</div>