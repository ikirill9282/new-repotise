<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thank you for registering!</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; text-align: center;">

    <div style="width: 100%; padding: 0; line-height: 0;">
        {{-- <img src="{{ url('/storage/images/home_filter.png') }}" alt="Баннер" style="max-width: 100%; height: auto; display: block; margin: 0 auto;"> --}}
        <img src="http://trekguider.com/assets/img/bg_home_tips.png" alt="Banner" style="max-width: 100%; object-fit: cover;">
    </div>

    <div style="padding: 20px;">
        <h2 style="margin-top: 20px;">Welcome to TrekGuider! Let's Confirm Your Email</h2>
        <p>
            Hi there,
            Welcome to TrekGuider! We're thrilled you've joined our community of travel explorers.
            Just one more step to get started: please confirm your email address by clicking the button below:
        </p>
        <a href="{{ $user->getVerifyUrl() ?? '' }}" style="display: inline-block; padding: 15px 20px; background: #FC7361; color: #fff; text-decoration: none; border-radius: 4px;">
              Confirm Registration
          </a>
        <p>
            Didn't sign up for TrekGuider? No worries, you can safely ignore this email.
            Adventure awaits,
            The TrekGuider Crew
        </p>
        <p>If you did not register on our site, please ignore this email.</p>
    </div>
</body>
</html>
