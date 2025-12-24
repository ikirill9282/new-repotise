@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <div class="">
      <x-card size="sm" class="mb-10">
        @livewire('profile.tables.profile-reviews')
      </x-card>
      <x-card size="sm" class="">
        @livewire('profile.tables.profile-refunds')
      </x-card>
    </div>
  </x-profile.wrap>
@endsection

@push('js')
  <script>
    (function () {
      const HEADER_OFFSET = 110; // account for fixed header + spacing
      const shouldHandle = (hash) => {
        return (
          hash === '#reviews' ||
          hash === '#refunds' ||
          hash.startsWith('#refund-')
        );
      };

      const scrollToHash = () => {
        const hash = window.location.hash || '';
        if (!hash || !shouldHandle(hash)) return;

        const el = document.querySelector(hash);
        if (!el) return;

        const top = el.getBoundingClientRect().top + window.pageYOffset - HEADER_OFFSET;
        window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
      };

      // Browser may over/under-scroll when layout stabilizes (images/fonts/livewire),
      // so run once on load and once shortly after.
      window.addEventListener('load', () => {
        setTimeout(scrollToHash, 0);
        setTimeout(scrollToHash, 250);
      }, { once: true });
    })();
  </script>
@endpush