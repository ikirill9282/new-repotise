@extends('layouts.email')

@section('content')
<div class="main">
    <div class="bg"></div>
    <div class="relative">
        <h1>Welcome to TrekGuider & Your Gift…</h1>
        <p class="mb-10">
            Thank you for joining TrekGuider and for your gift purchase! Your gift has been sent to the recipient.
        </p>
        
        <p class="mb-10">
            <strong>Recipient:</strong> {{ $gift->recipient_email }}<br>
            @if($order->recipient_message)
                <strong>Your message:</strong> {{ $order->recipient_message }}
            @endif
        </p>

        <p class="mb-10">
            To get started, please set your password:
        </p>

        <div class="button">
            <a class="btn" href="{{ route('home') }}?modal=set-password&email={{ urlencode($buyer->email) }}">Set Your Password</a>
        </div>
    </div>
</div>
@endsection
