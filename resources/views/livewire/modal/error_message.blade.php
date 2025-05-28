
@if(array_key_exists($key, $this->errors))
<span class="inline-block text-sm text-red-500 mt-2">
  {{ $this->errors[$key] }}
</span>
@php
  unset($this->errors[$key])
@endphp
@endif