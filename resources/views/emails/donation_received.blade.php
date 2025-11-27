@extends('layouts.email')

@section('content')
<div class="main">
    <div class="bg"></div>
    <div class="relative">
        <h1>You Received a Donation!</h1>
        <p class="mb-10">
            You received a donation of <strong>${{ number_format($amount, 2) }}</strong>
            @if($anonymous)
                from an anonymous donor.
            @elseif($donorName)
                from {{ $donorName }}.
            @else
                from a supporter.
            @endif
        </p>
        
        @if($message)
            <div class="mb-10">
                <p><strong>Message:</strong></p>
                <p>{{ $message }}</p>
            </div>
        @endif

        <p>
            The donation has been credited to your platform balance. You can view all your donations in the "Donations" section of your seller account.
        </p>
    </div>
</div>
@endsection

