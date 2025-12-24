<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf" content="{{ csrf_token() }}">
		<meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mulish:ital,wght@0,200..1000;1,200..1000&family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/jquery.toast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style3.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/site.css') }}">

    <script src="{{ asset('/assets/js/jquery.min.js') }}"></script>
    
    <link rel="stylesheet" href="{{ asset('/assets/css/jquery.emojipicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/jquery.emojipicker.a.css') }}">
    
    
    <script src="https://cdn.jsdelivr.net/npm/@meilisearch/instant-meilisearch/dist/instant-meilisearch.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/instantsearch.js@4"></script>
    <!-- Include only the reset -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.css@8.5.1/themes/reset-min.css" integrity="sha256-KvFgFCzgqSErAPu6y9gz/AhZAvzK48VJASu3DpNLCEQ=" crossorigin="anonymous"> --}}

    <!-- or include the full Satellite theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.css@8.5.1/themes/satellite-min.css" integrity="sha256-woeV7a4SRDsjDc395qjBJ4+ZhDdFn8AqswN1rlTO64E=" crossorigin="anonymous">

    <link rel="icon" type="image/svg+xml" href="{{ asset('/favicon.svg') }}">
    <title>@yield('title', 'TrekGuider')</title>
		<!-- {{-- @yield('title', config('app.name')) --}} -->

    @livewireStyles

    @vite('resources/css/app.css')

    @stack('css')

    @stack('head')

    @php
      $cart = new \App\Services\Cart();
      $ga4MeasurementId = ga4_measurement_id();
    @endphp

    @if($ga4MeasurementId)
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4MeasurementId }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $ga4MeasurementId }}', {
        'send_page_view': true,
        'anonymize_ip': true, // GDPR compliance
        'cookie_flags': 'SameSite=None;Secure'
      });
    </script>
    @endif
    
    @php
      $recaptchaSiteKey = config('services.recaptcha.site_key');
    @endphp
    @if($recaptchaSiteKey)
    <!-- Google reCAPTCHA v2 (explicit render) -->
    <script>
      // Global reCAPTCHA callback registry (supports multiple widgets across pages/modals)
      window.recaptchaCallbacks = window.recaptchaCallbacks || [];
      window.onRecaptchaV2Load = function () {
        (window.recaptchaCallbacks || []).forEach(function (callback) {
          try {
            if (typeof callback === 'function') callback();
          } catch (e) {
            console.error('reCAPTCHA callback error:', e);
          }
        });
      };
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaV2Load&render=explicit" async defer></script>
    @endif
</head>

<body class="text-dark">
      @stack('before_header')
      
      @include('site.header')

      <main class="" id="main">
        @yield('content')
      </main>

      @include('site.footer')

      {{-- @livewire('modal') --}}
      @livewire('modals')

    @include('site.components.accept_cookie')
    
    @livewireScripts
    
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <script>
      let parallaxes = new makeParallax();
      $(window).on('resize', function() {
        parallaxes = new makeParallax();
      });
    </script>
    <script src="{{ asset('/assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.toast.min.js') }}"></script>
    <script src="{{ asset('/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/js/search.js') }}"></script>
    <script src="{{ asset('/assets/js/main.js') }}"></script>

    <script src="{{ asset('/assets/js/jquery.emojipicker.a.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.emojipicker.js') }}"></script>

    <script src="{{ asset('/assets/js/app.js') }}"></script>
    <script src="{{ asset('/assets/js/cookie-manager.js') }}"></script>

    @vite('resources/js/app.js')

    @stack('js')

    @yield('js')

    {{-- Controller-driven toasts (for non-Livewire redirects) --}}
    @if(session()->has('toast_success') || session()->has('toast_error'))
      <script>
        (function () {
          const success = @js(session('toast_success'));
          const error = @js(session('toast_error'));

          function normalizeToast(value, fallbackHeading) {
            if (!value) return null;
            if (typeof value === 'string') {
              return { message: value, heading: fallbackHeading };
            }
            if (typeof value === 'object') {
              return {
                message: value.message || value.text || '',
                heading: value.heading || fallbackHeading,
              };
            }
            return null;
          }

          const successToast = normalizeToast(success, 'Success');
          const errorToast = normalizeToast(error, 'Error');

          if (successToast && successToast.message) {
            $.toast({
              text: successToast.message,
              icon: 'success',
              heading: successToast.heading,
              position: 'top-right',
              hideAfter: 5000,
            });
          }

          if (errorToast && errorToast.message) {
            $.toast({
              text: errorToast.message,
              icon: 'error',
              heading: errorToast.heading,
              position: 'top-right',
              hideAfter: 5000,
            });
          }
        })();
      </script>
    @endif

    {{-- Deferred toast notifications (stored in DB for authenticated users) --}}
    @auth
      @php
        $toastNotify = \App\Models\UserNotification::query()
          ->where('user_id', auth()->id())
          ->where('show', 1)
          ->where('group', 'toast')
          ->orderByDesc('id')
          ->first();

        $toastPayload = null;
        if ($toastNotify && is_string($toastNotify->message) && $toastNotify->message !== '') {
          $decoded = json_decode($toastNotify->message, true);
          if (is_array($decoded)) {
            $toastPayload = $decoded;
          }
        }

        if ($toastNotify) {
          $toastNotify->update(['show' => 0]);
        }
      @endphp

      @if(!empty($toastPayload['message']))
        <script>
          (function () {
            $.toast({
              text: @js($toastPayload['message']),
              icon: @js($toastPayload['icon'] ?? 'success'),
              heading: @js($toastPayload['heading'] ?? (($toastPayload['icon'] ?? 'success') === 'error' ? 'Error' : 'Success')),
              position: 'top-right',
              hideAfter: 5000,
            });
          })();
        </script>
      @endif
    @endauth
</body>

</html>
