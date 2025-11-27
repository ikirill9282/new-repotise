@extends('layouts.email')

@section('content')
<div class="main">
    <div class="bg"></div>
    <div class="relative">
        @if($monthlySupport)
            <h1>Monthly Support Subscription Confirmed</h1>
            <p class="mb-10">
                You successfully subscribed to monthly support for <strong>{{ $seller->getName() }}</strong>. 
                Your first payment of <strong>${{ number_format($amount, 2) }}</strong> has been processed.
            </p>
            <p>
                You can manage or cancel your subscription anytime in your <a href="{{ route('profile.settings') }}" style="color: #007bff; text-decoration: underline;">account settings</a>.
            </p>
        @else
            <h1>Donation Confirmation</h1>
            <p class="mb-10">
                Your donation of <strong>${{ number_format($amount, 2) }}</strong> for <strong>{{ $seller->getName() }}</strong> has been successfully sent.
            </p>
            <p>
                Thank you for supporting this creator!
            </p>
        @endif
    </div>
</div>
@endsection

