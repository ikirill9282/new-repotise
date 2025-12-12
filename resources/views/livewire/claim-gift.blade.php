@if($gift && $gift->canBeClaimed())
    @if(Auth::check())
        @php
            $user = Auth::user();
            $canClaim = $user->email === $gift->recipient_email || $gift->buyer_user_id === $user->id;
        @endphp
        @if($canClaim)
            <button wire:click="claim" class="claim_gift">
                Claim Your Gift
            </button>
        @else
            <div class="text-red-500 text-sm">
                This gift was sent to {{ $gift->recipient_email }}. Please log in with that email address.
            </div>
        @endif
    @else
        <a href="#" class="claim_gift open_auth" wire:click.prevent="$dispatch('openModal', {modalName: 'auth'})">
            Claim Your Gift
        </a>
    @endif
@endif
