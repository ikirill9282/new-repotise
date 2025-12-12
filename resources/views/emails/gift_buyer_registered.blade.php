@extends('layouts.email')

@section('content')
<div class="main">
    <div class="bg"></div>
    <div class="relative">
        <h1>You Just Gifted Some Adventure…</h1>
        <p class="mb-10">
            Thank you for your gift purchase! Your gift has been sent to the recipient.
        </p>
        
        <p class="mb-10">
            <strong>Recipient:</strong> {{ $gift->recipient_email }}<br>
            @if($order->recipient_message)
                <strong>Your message:</strong> {{ $order->recipient_message }}
            @endif
        </p>

        <p>
            The recipient will receive an email with instructions on how to claim their gift.
        </p>
    </div>
</div>
@endsection
