@props([
  'user' => null,
])

@php
  $referals_count = $user?->referal_buyers()->count();
  $delimeter = $referals_count > 0 ? ($referals_count % 10) : 0;
  $ost = 10 - $delimeter;
@endphp

<div class="progresss">
  <span>
    {{ str_ireplace('[X]', $ost, (string)$slot) }}
  </span>
  <div data-progress="3" class="progresss-item">
    @for($i = 0; $i <= 10; $i++)
      <i class="{{ $i <= $delimeter ? 'active' : '' }} {{ $i == $delimeter ? 'last' : '' }}">
        <span class="active-percent">{{ $i == $delimeter ? ($i > 0 ? $i : '')."0%" : '' }}</span>
      </i>
    @endfor
  </div>
</div>