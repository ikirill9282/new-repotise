@extends('layouts.site')

@section('content')
    <section class="hero__investorsInvite relative">
      @include('site.components.parallax', ['class' => 'parallax-investments'])
      <div class="container relative z-10">
        <h1 class="hero__investorsInvite-title">
          Partner with TrekGuider: Expand Your Reach & Revenue in the Digital Travel Market
        </h1>
        <h5 class="hero__investorsInvite-subtitle">
          Reach a global audience of passionate travelers, boost your brand visibility, and unlock new revenue
          streams. TrekGuider offers a powerful platform with cutting-edge tools, flexible monetization, and a
          thriving community—your gateway to the future of digital travel.
        </h5>
        <button class="main-btn hero__investorsInvite-btn">Explore Partnership</button>
      </div>
    </section>
    <section class="classes">
      <div class="container">
        <h4 class="classes__title section-title">Join Our Growing Network</h4>
        <ul class="classes__list">
          <div class="classes__item-wrapper-top">
            <li class="classes__item">
              <p class="classes__item-title">Travel Experts <br />& Content Creators</p>
              <p class="classes__item-subtitle">
                Monetize your travel experience, sell guides, itineraries, maps and more.
              </p>
            </li>
            <li class="classes__item">
              <p class="classes__item-title">Tourism Businesses <br />& Services</p>
              <p class="classes__item-subtitle">
                Offer your tours, attractions, and services directly to active travel planners seeking expert
                recommendations.
              </p>
            </li>
            <li class="classes__item">
              <p class="classes__item-title">Business Consultants <br />& Advisors</p>
              <p class="classes__item-subtitle">
                Help shape our growth strategy, optimize our business model, and guide us toward new market
                opportunities.
              </p>
            </li>
          </div>
          <div class="classes__item-wrapper">
            <li class="classes__item">
              <p class="classes__item-title">Investors <br />& Advisors</p>
              <p class="classes__item-subtitle">
                Invest at a pivotal moment. With a proven model and ambitious expansion into tours, events, and
                interactive maps, we're transforming digital travel.
              </p>
            </li>
            <li class="classes__item">
              <p class="classes__item-title">Technology <br />& Platform Integrations</p>
              <p class="classes__item-subtitle">
                Integrate your APIs, analytics, or tech solutions to enhance our platform and user experience.
              </p>
            </li>
          </div>
        </ul>
      </div>
    </section>
    <section class="partner">
      <div class="container">
        <h4 class="partner__title section-title">Why Partner With Our Marketplace?</h4>
        <ol class="partner__list">
          <li class="partner__item">
            <h4 class="partner__item-title">Drive More Bookings & Leads</h4>
            <p class="partner__item-subtitle">
              Showcase your services to an audience ready to take action, from booking tours to purchasing digital
              products.
            </p>
          </li>
          <li class="partner__item">
            <h4 class="partner__item-title">Future-Proof Your <br />Business</h4>
            <p class="partner__item-subtitle">
              Benefit from our continuously evolving platform, with upcoming features like interactive maps, event
              bookings, and advanced business tools.
            </p>
          </li>
          <li class="partner__item">
            <h4 class="partner__item-title">Benefit From Advanced Analytics</h4>
            <p class="partner__item-subtitle">
              Make data-driven decisions with real-time insights on sales, user engagement, and conversion rates.
            </p>
          </li>
        </ol>
        <ol class="partner__list-flex">
          <li class="partner__item">
            <h4 class="partner__item-title">Reach Your Ideal Audience</h4>
            <p class="partner__item-subtitle">
              Access a global network of travelers actively seeking trusted information and memorable experiences.
            </p>
          </li>
          <li class="partner__item">
            <h4 class="partner__item-title">Build Brand Authority</h4>
            <p class="partner__item-subtitle">
              Elevate your brand alongside reputable travel content, strengthening credibility and consumer trust.
            </p>
          </li>
        </ol>
      </div>
    </section>
    <section class="ways">
      <div class="wrapper">
        <div class="container">
          <h4 class="ways__title section-title">Ways to Partner</h4>
          <ul class="ways__list">
            <li class="ways__item">
              <div class="ways__item-wrapper">
                <p class="ways__item-title">Digital Product Showcases:</p>
                <p class="ways__item-subtitle">
                  Showcase your tours, attractions, or digital products directly within relevant travel guides. Reach
                  travelers ready to book and explore
                </p>
              </div>
              <img src="{{ asset('assets/img/icons/desktop.svg') }}" alt="" class="ways__item-icon" />
            </li>
            <li class="ways__item">
              <div class="ways__item-wrapper">
                <p class="ways__item-title">Custom Guides:</p>
                <p class="ways__item-subtitle">
                  Commission exclusive, branded digital guides. Offer immersive experiences that highlight your brand
                  and inspire deeper exploration.
                </p>
              </div>
              <img src="{{ asset('assets/img/icons/book.svg') }}" alt="" class="ways__item-icon" />
            </li>
            <li class="ways__item">
              <div class="ways__item-wrapper">
                <p class="ways__item-title">Platform Integrations:</p>
                <p class="ways__item-subtitle">
                  Integrate your APIs, analytics, or tech solutions to enhance our platform and user experience. Offer
                  seamless journeys through technology.
                </p>
              </div>
              <img src="{{ asset('assets/img/icons/integration.svg') }}" alt="" class="ways__item-icon" />
            </li>
            <li class="ways__item">
              <div class="ways__item-wrapper">
                <p class="ways__item-title">Sponsored Content:</p>
                <p class="ways__item-subtitle">
                  Share your expertise and brand story through sponsored content. Position yourself as a trusted
                  authority and engage a passionate travel audience.
                </p>
              </div>
              <img src="{{ asset('assets/img/icons/content.svg') }}" alt="" class="ways__item-icon" />
            </li>
            <li class="ways__item">
              <div class="ways__item-wrapper">
                <p class="ways__item-title">Cross-Promotions:</p>
                <p class="ways__item-subtitle">
                  Collaborate on co-branded campaigns and social media initiatives. Expand your reach, share
                  audiences, and amplify your marketing ROI.
                </p>
              </div>
              <img src="{{ asset('assets/img/icons/sale.svg') }}" alt="" class="ways__item-icon" />
            </li>
            <li class="ways__item">
              <div class="ways__item-wrapper">
                <p class="ways__item-title">Investment Partnerships:</p>
                <p class="ways__item-subtitle">
                  Invest in TrekGuider's pivotal growth phase. Shape the future of digital travel and reap the rewards
                  of a thriving marketplace.
                </p>
              </div>
              <img src="{{ asset('assets/img/icons/investment.svg') }}" alt="" class="ways__item-icon" />
            </li>
          </ul>
        </div>
      </div>
    </section>
    <section class="formConnect">
      <div class="container">
        <div class="formConnect-wrapper">
          <div class="formConnect-content">
            <h4 class="formConnect__title section-title">Let's Connect</h4>
            <p class="formConnect__subtitle">
              Ready to explore how partnering with TrekGuider can benefit your business? Reach out to our partnership
              team today!
            </p>
            <form action="" class="formConnect__form">
              <div class="formConnect__companyName-element">
                <input type="text" class="formConnect__companyName" placeholder="Company name" />
              </div>
              <div class="formConnect__companyName-element">
                <select name="" id="" class="formConnect__form-select">
                  <option value="" selected hidden>Select a topic</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
              </div>
              <div class="formConnect__companyName-element">
                <textarea
                  name=""
                  id="messageTextarea"
                  class="formConnect__massege"
                  maxlength="500"
                  placeholder="Text your message"
                ></textarea>
                <div class="counter">
                  <span class="current">0</span>/
                  <span class="max">500</span>
                </div>
              </div>
              <button class="main-btn formConnect__form-btn">Start Partnership</button>
            </form>
          </div>
          <img src="{{ asset('assets/img/formImage.png') }}" alt="" class="formConnect__image" />
        </div>
      </div>
    </section>
@endsection