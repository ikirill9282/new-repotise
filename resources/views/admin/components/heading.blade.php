@php
$headers = [
    'Header h1' => 'h1',
    'Header h2' => 'h2',
    'Header h3' => 'h3',
    'Header h4' => 'h4',
    'Header h5' => 'h5',
    'Header h6' => 'h6',
];
@endphp
@foreach ($headers as $title => $header)
  <option value="{{ $header }}" >{{ $title }}</option>
@endforeach