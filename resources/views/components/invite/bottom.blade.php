<section class="invite__bottom">
    <div class="container">
        <div class="invite-bottom__wrap">
            <div class="invite-bottom__text">
                <h2>
                    Your Time to Monetize is Now. Start Selling Travel Content on
                    TrekGuider!
                </h2>
                <p>
                    Join a community of successful travel creators and start
                    monetizing your passion and expertise today.
                </p>

                <x-btn class="{{ auth()->check() ? '' : 'open_auth' }} mt-4"
                    href="{{ auth()->check() ? route('profile') : '#' }}">Start Earning Today</x-btn>

            </div>
            <div class="invite-bottom__img">
                <img src="{{ asset('assets/img/invite-bottom.png') }}" alt="" />
            </div>
        </div>
    </div>
</section>
