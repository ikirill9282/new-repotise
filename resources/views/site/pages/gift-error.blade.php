@extends('layouts.site')

@section('content')
    <section class="gift_home relative">
        @include('site.components.parallax', ['class' => 'parallax-gift'])
        <div class="container relative z-10">
            <div class="about_block">
                <h1>{{ $title }}</h1>
                @include('site.components.breadcrumbs')
            </div>
        </div>
    </section>
    <section class="you_gifted">
        <div class="container">
            <div class="about_block">
                <div class="left_block text-center">
                    <h2>{{ $title }}</h2>
                    <p class="mb-4">{{ $text }}</p>
                    <a href="{{ route('home') }}" class="claim_gift">Go to Homepage</a>
                </div>
            </div>
        </div>
    </section>
@endsection
