@extends('layouts.site')

@section('content')
  <div class="the-content !bg-light">
    
    {{-- HERO --}}
    <section class="invite-hero hero custom relative">
      @include('site.components.parallax', ['class' => 'parallax-referal'])
      <div class="container">
        <div x-data="{}" class="invite-hero__text hero__text">
          <h1>Referral Program</h1>
          <p>
            Your influence is your asset. Turn your network into net worth. Share your unique link now and watch your rewards multiply! Enjoy exclusive discounts, free products, and earn passive income with up to 25% of seller commissions. Every share boosts your income!
          </p>

          @if(auth()->check())
            <x-btn href="{{ route('profile.referal') }}">Start Now</x-btn>
          @else
            <x-btn x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'register' })">Start Now</x-btn>
          @endif

        </div>
      </div>
    </section>
    
    {{-- MAIN CARD --}}
    <section class="pt-25 pb-12">
      <div class="container">
        <x-card class="border-1 border-gray/50 lg:!p-12">
          <h2 class="text-2xl text-dark text-center !mb-4 md:!mb-10">Double the Impact, Double the Rewards</h2>
          <div class="flex justify-between items-stretch flex-col md:flex-row">
            <div class="py-4 basis-1/2 text-center">
              <div class="bg-light mb-3 !py-8 flex justify-center items-center !gap-3 lg:!gap-8">
                <img class="max-xs:w-[14vw]" src="{{ asset('assets/img/consumer.svg') }}" alt="Consumer">
                <img class="max-xs:w-[14vw]" src="{{ asset('assets/img/discount.svg') }}" alt="Discount">
                <img class="max-xs:w-[14vw]" src="{{ asset('assets/img/gift.svg') }}" alt="Gift">
              </div>
              <div class="font-bold text-xl mb-3">Invite Buyers:</div>
              <div class="text-sm">Invite a buyer and start earning rewards immediately. When your friend signs up, they enjoy a 15% discount on their first digital purchase (up to $50) while you receive a 15% off promo code for your next order. Plus, every 10 successful buyer referrals earns you a bonus free product. It’s a win-win!</div>
            </div>
            <div class="my-4 md:my-0 md:mx-8 lg:mx-12 w-full h-[1px] md:h-auto md:w-[1px] rounded-full bg-gray/25"></div> {{-- Line --}}
            <div class="py-4 basis-1/2 text-center">
              <div class="bg-light mb-3 !py-8 flex justify-center items-center !gap-4 lg:!gap-8">
                <img class="max-xs:w-[14vw]" src="{{ asset('assets/img/market.svg') }}" alt="Market">
                <img class="max-xs:w-[14vw]" src="{{ asset('assets/img/income.svg') }}" alt="Income">
                <img class="max-xs:w-[14vw]" src="{{ asset('assets/img/economic.svg') }}" alt="Economic">
                <img class="max-xs:w-[14vw]" src="{{ asset('assets/img/cloud.svg') }}" alt="Cloud">
              </div>
              <div class="font-bold text-xl mb-3">Invite Sellers:</div>
              <div class="text-sm">Help new sellers kickstart their journey and earn powerful passive income! New sellers launch with an exclusive 4% commission on all sales during their first 30 days and get an extra 1GB of storage. In return, you earn 25% of the platform’s commission on their sales in the first month – plus an additional 12.5% for the next 11 months. Every referral boosts your earnings!</div>
            </div>
          </div>
        </x-card>
      </div>
    </section>

    {{-- STEPS --}}
    <section class="invite-launch">
      <div class="container">
        <div class="invite-launch__title">
          <h3 class="section-title">
            How does this work?
          </h3>
        </div>
        <div class="invite-launch__items !m-0 gap-4">
          <div class="item !m-0 !w-auto basis-1/3">
            <div class="num">
              <span>1</span>
            </div>
            <div class="text">
              <h3>Get Your Referral Link</h3>
              <p>Get your unique referral link right in your account.</p>
            </div>
          </div>
          <div class="item !m-0 !w-auto basis-1/3">
            <div class="num">
              <span>2</span>
            </div>
            <div class="text">
              <h3>Share the Link</h3>
              <p>Share your link with friends, followers and social networks.</p>
            </div>
          </div>
          <div class="item !m-0 !w-auto basis-1/3">
            <div class="num">
              <span>3</span>
            </div>
            <div class="text">
              <h3>Earn Rewards</h3>
              <p>Get bonuses and passive income with every new successful referral.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    {{-- FAQ --}}
    <section class="pb-25 pt-12">
      <div class="container">
        <x-card class="border-1 border-gray/50 accordion">
          <h4 class="font-bold text-3xl !mb-6">FAQ – Frequently Asked Questions</h4>
          <x-accordion :items="[
            [
              'title' => 'How much can I earn?',
              'text' => 'There’s no limit – the more referrals, the higher your earnings. For example, if you refer 100 sellers with an average annual revenue of $10K, you could earn over $5,400 in passive income over 12 months.',
            ],
            [
              'title' => 'How are referral bonuses paid out and when will they appear in my account?',
              'text' => 'There’s no limit – the more referrals, the higher your earnings. For example, if you refer 100 sellers with an average annual revenue of $10K, you could earn over $5,400 in passive income over 12 months.',
            ],
            [
              'title' => 'Is there a verification process for withdrawals?',
              'text' => 'There’s no limit – the more referrals, the higher your earnings. For example, if you refer 100 sellers with an average annual revenue of $10K, you could earn over $5,400 in passive income over 12 months.',
            ],
            [
              'title' => 'What qualifies as a successful referral?',
              'text' => 'There’s no limit – the more referrals, the higher your earnings. For example, if you refer 100 sellers with an average annual revenue of $10K, you could earn over $5,400 in passive income over 12 months.',
            ],
            [
              'title' => 'Who can participate in the program?',
              'text' => 'There’s no limit – the more referrals, the higher your earnings. For example, if you refer 100 sellers with an average annual revenue of $10K, you could earn over $5,400 in passive income over 12 months.',
            ],
            [
              'title' => 'How can I track my referral earnings and progress?',
              'text' => 'There’s no limit – the more referrals, the higher your earnings. For example, if you refer 100 sellers with an average annual revenue of $10K, you could earn over $5,400 in passive income over 12 months.',
            ],
            [
              'title' => 'Who should I contact if I have issues with the referral program?',
              'text' => 'There’s no limit – the more referrals, the higher your earnings. For example, if you refer 100 sellers with an average annual revenue of $10K, you could earn over $5,400 in passive income over 12 months.',
            ],
          ]" />
        </x-card>
      </div>
    </section>
  </div>
@endsection