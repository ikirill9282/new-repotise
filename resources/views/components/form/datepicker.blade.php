@props([
  'id' => 'id'.uniqid(),
  'name' => uniqid(),
  'label' => null,
  'tooltip' => false,
  'tooltipText' => null,
])

@once
@push('css')
<style>
  /* Кастомные стили для AirDatepicker в красно-оранжевой палитре */
  .air-datepicker {
    --adp-color-primary: #FC7361 !important;
    --adp-color-primary-hover: #e86552 !important;
    --adp-color-current-date: #FC7361 !important;
    --adp-color-selected-date: #ffffff !important;
    --adp-color-selected-date-background: #FC7361 !important;
    --adp-color-day-name: #212529 !important;
    --adp-color-day-name-hover: #FC7361 !important;
    --adp-color-cell-hover: rgba(252, 115, 97, 0.15) !important;
    --adp-color-cell-selected: #ffffff !important;
    --adp-color-cell-selected-background: #FC7361 !important;
    --adp-color-cell-selected-hover: #e86552 !important;
    --adp-color-cell-disabled: #A4A0A0 !important;
    --adp-color-cell-disabled-background: #f5f5f5 !important;
    --adp-color-cell-other-month: #A4A0A0 !important;
    --adp-color-cell-other-month-hover: rgba(252, 115, 97, 0.1) !important;
    --adp-color-nav-arrow-hover: #FC7361 !important;
    --adp-color-nav-action-hover: #FC7361 !important;
    --adp-color-nav-action-active: #FC7361 !important;
    --adp-color-button-hover: rgba(252, 115, 97, 0.1) !important;
    --adp-color-button-active: #FC7361 !important;
    --adp-color-button-active-text: #ffffff !important;
    --adp-color-button-text: #212529 !important;
    --adp-color-button-text-hover: #FC7361 !important;
    --adp-border-radius: 8px;
    --adp-font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    --adp-font-size: 14px;
    --adp-width: 300px;
    --adp-z-index: 100;
    box-shadow: 0 4px 16px rgba(252, 115, 97, 0.2) !important;
    border: 1px solid rgba(252, 115, 97, 0.3) !important;
    background-color: #ffffff;
  }

  .air-datepicker--pointer {
    --adp-pointer-color: #FC7361 !important;
  }
  
  .air-datepicker--pointer::before {
    border-bottom-color: #FC7361 !important;
  }

  .air-datepicker-nav {
    border-bottom: 1px solid rgba(252, 115, 97, 0.15) !important;
    padding: 12px 16px;
    background-color: #ffffff;
  }

  .air-datepicker-nav--title {
    color: #212529 !important;
    font-weight: 600;
  }

  .air-datepicker-nav--title:hover {
    color: #FC7361 !important;
  }

  .air-datepicker-nav--action {
    color: #212529 !important;
    transition: all 0.2s ease;
  }

  .air-datepicker-nav--action:hover {
    color: #FC7361 !important;
    background-color: rgba(252, 115, 97, 0.1) !important;
  }

  .air-datepicker-nav--action svg {
    fill: currentColor;
  }

  /* Стили для названий дней недели */
  .air-datepicker--day-name {
    color: #212529 !important;
    font-weight: 600;
    font-size: 12px;
  }
  
  /* Выходные дни (суббота и воскресенье) - оранжевый цвет */
  .air-datepicker--day-name:nth-child(6),
  .air-datepicker--day-name:nth-child(7) {
    color: #FC7361 !important;
    font-weight: 700;
  }

  /* Ячейки календаря */
  .air-datepicker-cell {
    color: #212529 !important;
    transition: all 0.2s ease;
  }

  .air-datepicker-cell:hover {
    background-color: rgba(252, 115, 97, 0.15) !important;
    color: #FC7361 !important;
  }

  /* Выбранная дата */
  .air-datepicker-cell.-selected- {
    background-color: #FC7361 !important;
    color: #ffffff !important;
    font-weight: 600;
    border-radius: 4px;
  }

  .air-datepicker-cell.-selected-:hover {
    background-color: #e86552 !important;
    color: #ffffff !important;
  }

  /* Текущая дата (сегодня) */
  .air-datepicker-cell.-current- {
    color: #FC7361 !important;
    font-weight: 600;
  }

  .air-datepicker-cell.-current-.-selected- {
    background-color: #FC7361 !important;
    color: #ffffff !important;
  }

  /* Отключенные даты */
  .air-datepicker-cell.-disabled- {
    color: #A4A0A0 !important;
    background-color: #f5f5f5 !important;
    cursor: not-allowed;
    opacity: 0.5;
  }

  /* Даты из других месяцев */
  .air-datepicker-cell.-other-month- {
    color: #A4A0A0 !important;
    opacity: 0.4;
  }

  .air-datepicker-cell.-other-month-:hover {
    background-color: rgba(252, 115, 97, 0.05) !important;
    color: #FC7361 !important;
  }

  /* Кнопки внизу календаря */
  .air-datepicker--buttons {
    border-top: 1px solid rgba(252, 115, 97, 0.15) !important;
    padding: 8px;
    background-color: #ffffff;
  }

  .air-datepicker--button {
    color: #212529 !important;
    transition: all 0.2s ease;
    border-radius: 4px;
    padding: 6px 12px;
  }

  .air-datepicker--button:hover {
    background-color: rgba(252, 115, 97, 0.1) !important;
    color: #FC7361 !important;
  }

  .air-datepicker--button.-active- {
    background-color: #FC7361 !important;
    color: #ffffff !important;
  }

  .air-datepicker--button.-active-:hover {
    background-color: #e86552 !important;
    color: #ffffff !important;
  }
  
  /* Переопределение стандартных цветов AirDatepicker */
  .air-datepicker-cell.-selected-,
  .air-datepicker-cell.-selected-.-current- {
    background: #FC7361 !important;
    color: #ffffff !important;
  }
  
  .air-datepicker-cell.-range-from-,
  .air-datepicker-cell.-range-to- {
    background: #FC7361 !important;
    color: #ffffff !important;
  }
  
  .air-datepicker-cell.-in-range- {
    background: rgba(252, 115, 97, 0.1) !important;
  }
</style>
@endpush
@endonce

<div class="relative datepicker"
  x-data="{
    picker: null,
  }"
  x-init="() => {
    picker = createDatePicker('#{{ $id }}');
  }"
>
  @if($label)
    <label class="!text-gray !mb-2" for="{{ $id }}">{{ $label }}</label>
  @endif
  <div class="bg-light !p-3 !pr-9 rounded-lg relative flex justify-between items-center datepicker-input">
    <div class="grow">
      <input x-ref="input" name="{{ $name }}" class="w-full !outline-0 datepicker-input" type="text" id="{{ $id }}" {{ $attributes }}>
    </div>
    <div x-on:click.prevent="() => $refs.input.focus()" class="!text-gray hover:cursor-pointer !px-1">
      @include('icons.calendar')
    </div>
    @if($tooltip && filled($tooltipText))
      <x-tooltip class="!right-3" :message="$tooltipText"></x-tooltip>
    @endif
  </div>
</div>