@extends('layouts.site')

@php

@endphp

@section('content')
  <section class="error">
    <div class="container !mx-auto">
        <div class="about_block">
            <img src="{{ asset('/assets/img/error.png') }}" alt="404" class="error_img">
            @include('site.components.heading')
            <h3>{!! print_var('subtitle', $variables) !!}</h3>
            @include('site.components.search', ['placeholder' => print_var('search_placeholder', $variables ?? null)])
            <div class="bottom_to_calatog">
                <a href="{{ print_var('product_link', $variables) }}" class="product_all">
                  {{ print_var('product_message', $variables) }}
                </a>
                <a href="{{ print_var('report_link', $variables) }}" class="report_problem">
                  {{ print_var('report_message', $variables) }}
                </a>
            </div>
        </div>
    </div>
  </section>
  @livewire('auth')
@endsection