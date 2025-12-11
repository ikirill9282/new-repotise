@if($gift && $gift->canBeClaimed())
    @if(Auth::check() && Auth::user()->email === $gift->recipient_email)
        <button wire:click="claim" class="claim_gift">
            Claim Your Gift
        </button>
    @else
        <a href="#" class="claim_gift open_auth" wire:click.prevent="$dispatch('openModal', {modalName: 'auth'})">
            Claim Your Gift
        </a>
    @endif
@endif
