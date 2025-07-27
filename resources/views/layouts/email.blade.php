<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    p {
      padding: 0;
      margin: 0;
    }
    .footer {
        margin-top: 40px;
        font-size: 12px;
        color: #999;
        text-align: center;
    }
  </style>
  @stack('css')
</head>
<body style="margin:0; padding: 0px; font-family: Arial, sans-serif; text-align: center;">
    @yield('content')

    <div class="footer">
        Best regards,<br>
        The trekguider.com Team<br>
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</body>
</html>