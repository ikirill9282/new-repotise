<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/svg+xml" href="{{ asset('/favicon.svg') }}">
  @vite('resources/css/app.css')
  <title>Admin Panel</title>
</head>
<body>
  
  @yield('content')

  @vite('resources/js/app.js')
</body>
</html>