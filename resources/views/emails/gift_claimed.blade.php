@extends('layouts.email')

@section('content')
<div class="main">
    <div class="bg"></div>
    <div class="relative">
        <h1>Great News! Your Gift… Has Been Claimed!</h1>
        <p class="mb-10">
            Great news! The recipient has successfully claimed the gift you sent.
        </p>
        
        <p class="mb-10">
            <strong>Recipient:</strong> {{ $gift->recipient_email }}<br>
            <strong>Claimed on:</strong> {{ $gift->claimed_at->format('F j, Y') }}
        </p>

        <p>
            The recipient now has full access to the products you gifted them.
        </p>
    </div>
</div>
@endsection
