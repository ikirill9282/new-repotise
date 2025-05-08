<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mulish:ital,wght@0,200..1000;1,200..1000&family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/site.css') }}">

    <script src="{{ asset('/assets/js/jquery.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('/assets/css/jquery.emojipicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/jquery.emojipicker.a.css') }}">
    
    
    <script src="https://cdn.jsdelivr.net/npm/@meilisearch/instant-meilisearch/dist/instant-meilisearch.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/instantsearch.js@4"></script>
    <!-- Include only the reset -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.css@8.5.1/themes/reset-min.css" integrity="sha256-KvFgFCzgqSErAPu6y9gz/AhZAvzK48VJASu3DpNLCEQ=" crossorigin="anonymous">

    <!-- or include the full Satellite theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.css@8.5.1/themes/satellite-min.css" integrity="sha256-woeV7a4SRDsjDc395qjBJ4+ZhDdFn8AqswN1rlTO64E=" crossorigin="anonymous">



    <link rel="icon" type="image/svg+xml" href="{{ asset('/favicon.svg') }}">
    <title>@yield('title', config('app.name'))</title>

    @livewireStyles
    @vite('resources/css/app.css')
    @stack('css')
</head>

<body>
      @stack('before_header')
      
      @include('site.header')

      <main class="" id="main">
        @yield('content')
      </main>

      @include('site.footer')

    {{-- @include('site.sections.accept_cookie') --}}
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <script>
      let parallaxes = new makeParallax();
      $(window).on('resize', function() {
        parallaxes = new makeParallax();
      });
    </script>
    <script src="{{ asset('/assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/js/search.js') }}"></script>
    <script src="{{ asset('/assets/js/main.js') }}"></script>

    <script src="{{ asset('/assets/js/jquery.emojipicker.a.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.emojipicker.js') }}"></script>

    @livewireScripts
    @vite('resources/js/app.js')

    @stack('js')

    @yield('js')
</body>

</html>
