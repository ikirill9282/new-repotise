<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thank you for registering!</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; text-align: center;">

    <div style="width: 100%; background-color: #4F8FF7; padding: 0;">
        {{-- <img src="{{ url('/storage/images/home_filter.png') }}" alt="Баннер" style="max-width: 100%; height: auto; display: block; margin: 0 auto;"> --}}
        <img src="http://trekguider.com/assets/img/bg_home_tips.png" alt="Banner">
    </div>

    <div style="padding: 20px;">
        <h2 style="margin-top: 20px;">Thank you for registering!</h2>
        <p>Please confirm your email by clicking the link below:</p>
        <p>
            <a href="{{ $url ?? '' }}" style="display: inline-block; padding: 10px 20px; background: #4F8FF7; color: #fff; text-decoration: none; border-radius: 4px; line-height: 0;">
              Confirm Registration
            </a>
        </p>
        <p>If you did not register on our site, please ignore this email.</p>
    </div>
</body>
</html>
