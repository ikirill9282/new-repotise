@extends('layouts.email')

@section('content')
<div class="main">
    <div class="bg"></div>
    <div class="relative">
        <h1>Surprise! {{ $order->buyer->getName() ?? 'Someone' }} Sent You a Gift…</h1>
        <p class="mb-10">
            You've received a gift on TrekGuider!
        </p>
        
        @if($order->recipient_message)
            <div class="mb-10">
                <p><strong>Message from {{ $order->buyer->getName() ?? 'the sender' }}:</strong></p>
                <p>{{ $order->recipient_message }}</p>
            </div>
        @endif

        <p class="mb-10">
            Click the button below to claim your gift:
        </p>

        <div class="button">
            <a class="btn" href="{{ route('gift', ['token' => $gift->token]) }}">Claim Your Gift</a>
        </div>
    </div>
</div>
@endsection
