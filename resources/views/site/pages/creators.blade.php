@extends('layouts.site')

@section('content')
    <section class="wiki__hero hero relative">
        @include('site.components.parallax', ['class' => 'parallax-creators'])
        <div class="container relative z-10">
            <h1 class="wiki__hero-title">Explore Creators</h1>

            <x-breadcrumbs 
              class="text-center !mb-4 !text-base"
              last="last:!text-white"
              listClass="!justify-center"
              :breadcrumbs="[
                'Home' => route('home'),
                'Creators' => route('creators'),
              ]"
            />

            <x-search action="{{ route('creators') }}" data-source="creators" placeholder="Search creators by name, topic, or channel...">
                @if(isset($tags) && !empty($tags))
                    <div div class="name_tags">
                        @foreach ($tags as $tag)
                            <a href="{{ route('creators') . '?creator=' . urlencode($tag['title']) . '' }}">{{ $tag['title'] }}</a>
                        @endforeach
                    </div>
                @endif
            </x-search>
        </div>
    </section>

    <section class="catalog !py-12 bg-light">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-[260px_1fr] lg:grid-cols-[300px_1fr] !gap-4 lg:!gap-10">
                
                {{-- FILTERS --}}
                <div class="col-span-1">
                    <form action="{{ route('creators') }}" mthod="GET" class="accordion bg-white rounded-lg" id="accordion">
                      <div class="accordion-header !p-3">
                          <button
                              class="accordion-button accordion-arrow !p-0 sm:!text-lg !text-black !shadow-none !bg-inherit
                            group-hover:!text-dark group-has-[.show]:!text-dark"
                              type="button" data-bs-toggle="collapse" data-bs-target="#collapse-main"
                              aria-controls="collapse-main" aria-expanded="true">
                              <span class="inline-block">Refine By</span>
                          </button>
                      </div>
                      <div id="collapse-main" class="accordion-collapse collapse show" data-bs-parent="accordion">
                          <div class="border-b-2 border-light"></div>
                          
                          {{-- FOLLOWERS --}}
                          <div class="!p-3">
                            <div class="" x-data="{
                                      minGap: 0,
                                      sliderOneValue: {{ request()->has('followers_min') ? request()->get('followers_min') : 0 }},
                                      sliderTwoValue: {{ request()->has('followers_max') ? request()->get('followers_max') : 1000000 }},
                                      sliderMinValue: 0,
                                      sliderMaxValue: 1000000,
                                      sliderOne(evt) {
                                          if (this.sliderOneValue === null) {
                                              this.sliderOneValue = this.sliderMinValue;
                                              this.fillColor();
                                              return;
                                          }
                                  
                                          if ((this.sliderOneValue - this.sliderTwoValue) >= 0) {
                                              this.sliderOneValue = this.sliderTwoValue;
                                              this.fillColor();
                                              return;
                                          }
                                  
                                          if (this.sliderOneValue < this.sliderMinValue) {
                                              this.sliderOneValue = this.sliderMinValue;
                                          }
                                  
                                          this.fillColor();
                                      },
                                      sliderTwo(evt) {
                                          if (this.sliderTwoValue === null) {
                                              this.sliderTwoValue = this.sliderMaxValue;
                                              this.fillColor();
                                              return;
                                          }
                                  
                                          if ((this.sliderTwoValue - this.sliderOneValue) <= 0) {
                                              this.sliderTwoValue = this.sliderOneValue;
                                              this.fillColor();
                                              return;
                                          }
                                  
                                          if (this.sliderTwoValue > this.sliderMaxValue) {
                                              this.sliderTwoValue = this.sliderMaxValue;
                                          }
                                  
                                          this.fillColor();
                                      },
                                      fillColor() {
                                          percent1 = this.sliderOneValue == 0 ? 0 : (this.sliderOneValue / this.sliderMaxValue) * 100;
                                          percent2 = (this.sliderTwoValue / this.sliderMaxValue) * 100;
                                          this.$refs.sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}% , rgb(252, 115, 97) ${percent1}% , rgb(252, 115, 97) ${percent2}%, #dadae5 ${percent2}%)`;
                                      }
                                  }" x-init="() => {
                                      sliderOne();
                                      sliderTwo();
                                  }">
                                <div class="!mb-4">Followers</div>
                                <div class="flex flex-col !gap-4">
                                    <div class="!h-1 relative w-full">
                                        <div x-ref="sliderTrack" class="h-full w-full rounded-full"
                                            style="background: linear-gradient(to right, rgb(218, 218, 229) 0%, rgb(252, 115, 97) 0%, rgb(252, 115, 97) 60%, rgb(218, 218, 229) 60%);">
                                        </div>
                                        <input x-model="sliderOneValue" x-on:input="(evt) => sliderOne(evt)"
                                            x-bind:min="sliderMinValue" x-bind:max="sliderMaxValue"
                                            class="absolute top-0 bottom-0 w-full !bg-transparent !outline-0 appearance-none range-input"
                                            type="range">
                                        <input x-model="sliderTwoValue" x-on:input="(evt) => sliderTwo(evt)"
                                            x-bind:min="sliderMinValue" x-bind:max="sliderMaxValue"
                                            class="absolute top-0 bottom-0 w-full !bg-transparent !outline-0 appearance-none range-input"
                                            type="range">
                                    </div>
                                    <div class="flex px-1 !text-gray !text-sm followers-inputs">
                                        <input x-model="sliderOneValue" x-on:input="(evt) => sliderOne(evt)"
                                            type="text" class="price-input followers-min" data-input="integer">
                                        <input x-model="sliderTwoValue" x-on:input="(evt) => sliderTwo(evt)"
                                            type="text" class="price-input text-right followers-max" data-input="integer">
                                    </div>
                                </div>
                            </div>
                          </div>

                          <div class="border-b-2 border-light"></div>


                          <div class="">

                              {{-- LANGUAGE --}}
                              <div class="accordion" id="lang">
                                <div class="accordion-header !p-3">
                                  <button
                                      class="accordion-button accordion-arrow collapsed !p-0 !font-noraml !text-base !text-black !shadow-none !bg-inherit
                                    group-hover:!text-dark group-has-[.show]:!text-dark"
                                      type="button" data-bs-toggle="collapse" data-bs-target="#collapse-lang"
                                      aria-controls="collapse-lang" aria-expanded="true">
                                      <span class="inline-block">Language</span>
                                  </button>
                                </div>
                                <div id="collapse-lang" class="accordion-collapse collapse relative" data-bs-parent="lang">
                                  <div class=" !p-3 !pt-0 text-[15px]">
                                    @include('site.components.search', [
                                        'icon' => false,
                                        'placeholder' => 'Language',
                                        'template' => 'filters',
                                        'hits' => 'filter-language',
                                        'attributes' => [
                                          'data-source' => 'language',
                                        ],
                                        'wrapClass' => 'flex items-center justify-start !gap-2 bg-light rounded !p-2 !py-3',
                                        'inputClass' => '!outline-0 w-full',
                                        'labelClass' => '!mb-0',
                                    ])
                                    <div class="input-group !mt-2">
                                        <div class="search_block">
                                            <div class="search_results language-results flex justify-start items-stretch !gap-2 flex-wrap">
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="border-b-2 border-light"></div>

                              {{-- COUNTRY --}}
                              <div x-data="{}" class="accordion" id="country">
                                <div class="accordion-header !p-3">
                                  <button
                                      
                                      class="accordion-button accordion-arrow collapsed !p-0 !font-noraml !text-base !text-black !shadow-none !bg-inherit
                                    group-hover:!text-dark group-has-[.show]:!text-dark"
                                      type="button" data-bs-toggle="collapse" data-bs-target="#collapse-country"
                                      aria-controls="collapse-country" aria-expanded="true">
                                      <span class="inline-block">Country</span>
                                  </button>
                                </div>
                                <div x-ref="collapse" id="collapse-country" class="accordion-collapse collapse relative" data-bs-parent="country" style="">
                                  <div class=" !p-3 !pt-0 text-[15px]">
                                    @include('site.components.search', [
                                        'icon' => false,
                                        'placeholder' => 'Country',
                                        'template' => 'filters',
                                        'hits' => 'filter-country',
                                        'attributes' => [
                                            'data-source' => 'country',
                                        ],
                                        'wrapClass' => 'flex items-center justify-start !gap-2 bg-light rounded !p-2 !py-3',
                                        'inputClass' => '!outline-0 w-full',
                                        'labelClass' => '!mb-0',
                                    ])
                                    <div class="input-group !mt-2">
                                        <div class="search_block">
                                            <div class="search_results country-results flex justify-start items-stretch !gap-2 flex-wrap">
                                            </div>
                                        </div>
                                    </div>

                                  </div>
                                </div>
                              </div>

                              <div class="border-b-2 border-light"></div>

                              {{-- PLATFORM --}}
                              <div class="accordion" id="platform">
                                <div class="accordion-header !p-3">
                                  <button
                                      class="accordion-button accordion-arrow !p-0 !font-noraml !text-base !text-black !shadow-none !bg-inherit
                                    group-hover:!text-dark group-has-[.show]:!text-dark"
                                      type="button" data-bs-toggle="collapse" data-bs-target="#collapse-platform"
                                      aria-controls="collapse-platform" aria-expanded="true">
                                      <span class="inline-block">Platforms</span>
                                  </button>
                                </div>
                                <div id="collapse-platform" class="accordion-collapse collapse show !pt-0 text-[15px]" data-bs-parent="platform">
                                  <div class="flex flex-col">
                                    <div class="border-b-2 border-light"></div>

                                    {{-- YOUTUBE --}}
                                    <div class="platform-item flex justify-start items-center !gap-2 !p-3 transition hover:cursor-pointer hover:bg-active hover:text-white" data-value="youtube">
                                      <div class="">
                                        <img class="!w-8" src="{{ asset('assets/img/icons/youtube.svg') }}" alt="YouTube">
                                      </div>
                                      <div class="grow">
                                        YouTube
                                      </div>
                                    </div>

                                    <div class="border-b-2 border-light"></div>

                                    {{-- TIKTOK --}}
                                    <div class="platform-item flex justify-start items-center !gap-2 !p-3 transition hover:cursor-pointer hover:bg-active hover:text-white" data-value="tiktok">
                                      <div class="">
                                        <img class="!w-8" src="{{ asset('assets/img/icons/tiktok.svg') }}" alt="TikTok">
                                      </div>
                                      <div class="grow">
                                        TikTok
                                      </div>
                                    </div>

                                    <div class="border-b-2 border-light"></div>

                                    {{-- INSTAGRAM --}}
                                    <div class="platform-item flex justify-start items-center !gap-2 !p-3 transition hover:cursor-pointer hover:bg-active hover:text-white" data-value="instagram">
                                      <div class="">
                                        <img class="!w-8" src="{{ asset('assets/img/icons/insta.svg') }}" alt="Instagram">
                                      </div>
                                      <div class="grow">
                                        Instagram
                                      </div>
                                    </div>

                                    <div class="border-b-2 border-light"></div>

                                    {{-- FACEBOOK --}}
                                    <div class="platform-item flex justify-start items-center !gap-2 !p-3 transition hover:cursor-pointer hover:bg-active hover:text-white" data-value="facebook">
                                      <div class="">
                                        <img class="!w-8" src="{{ asset('assets/img/icons/facebook.svg') }}" alt="Facebook">
                                      </div>
                                      <div class="grow">
                                        Facebook
                                      </div>
                                    </div>

                                    <div class="border-b-2 border-light"></div>

                                    {{-- GOOGLE --}}
                                    <div class="platform-item flex justify-start items-center !gap-2 !p-3 transition hover:cursor-pointer hover:bg-active hover:text-white" data-value="google">
                                      <div class="">
                                        <img class="!w-8" src="{{ asset('assets/img/icons/google.svg') }}" alt="Google">
                                      </div>
                                      <div class="grow">
                                        Google
                                      </div>
                                    </div>

                                    <div class="border-b-2 border-light"></div>

                                    {{-- XAI --}}
                                    <div class="platform-item flex justify-start items-center !gap-2 !p-3 transition hover:cursor-pointer hover:bg-active hover:text-white" data-value="xai">
                                      <div class="">
                                        <img class="!w-8" src="{{ asset('assets/img/icons/xai.svg') }}" alt="XAI">
                                      </div>
                                      <div class="grow">
                                        XAI
                                      </div>
                                    </div>

                                    <div class="border-b-2 border-light"></div>

                                    {{-- WEB1 --}}
                                    <div class="platform-item flex justify-start items-center !gap-2 !p-3 transition hover:cursor-pointer hover:bg-active hover:text-white" data-value="website">
                                      <div class="">
                                        <img class="!w-8" src="{{ asset('assets/img/icons/web.svg') }}" alt="Web">
                                      </div>
                                      <div class="grow">
                                        Website
                                      </div>
                                    </div>

                                    <div class="border-b-2 border-light"></div>

                                    {{-- WEB2 --}}
                                    <div class="platform-item flex justify-start items-center !gap-2 !p-3 transition hover:cursor-pointer hover:bg-active hover:text-white" data-value="other">
                                      <div class="">
                                        <img class="!w-8" src="{{ asset('assets/img/icons/web.svg') }}" alt="Web">
                                      </div>
                                      <div class="grow">
                                        Other Social Media
                                      </div>
                                    </div>

                                    <div class=""></div>
                                  </div>
                                </div>
                              </div>

                              <div class="border-b-2 border-light"></div>

                              {{-- COLLABORATION --}}
                              <div class="!p-3">
                                <x-form.checkbox :checked="request()->has('collaboration') && request()->get('collaboration')" id="collaboration" label="Open for Collaboration"></x-form.checkbox>
                              </div>

                              <div class="border-b-2 border-light"></div>

                              {{-- BUTTONS --}}
                              <div class="!p-3 text-center">
                                <x-btn class="!mb-3" id="apply-filters" >Apply</x-btn>
                                <x-link href="{{ route('creators') }}" class="!border-0">Clear Filters</x-link>
                              </div>
                          </div>
                      </div>
                    </form>
                </div>

                {{-- AUTHORS --}}
                <div class="col-span-1">
                  @if($creators->isEmpty())
									<div class="not_found_creators">
												<h3 class="text-center"> No creators found... for now <br>  Our community of creators is growing every day. Try broadening your search criteria.</h3>
												<img src="{{ asset('/assets/img/women_img.png') }}" alt=""
														class="women_img">
										</div>

                  @else
                    <div x-data="{}" class="flex justify-end items-center !mb-6">
                      <label class="text-gray" for="sorting-table">Sort By:</label>
                      <select
                        wire:model.live="sorting" 
                        id="sorting-table"
                        class="outline-0 pr-1 hover:cursor-pointer"
                        x-on:change="(evt) => {
                          const url = new URL(window.location.href);
                          const params = new URLSearchParams(url.search);
                          params.set('sort', evt.target.value);
                          url.search = params.toString();
                          window.location.href = url.toString();
                        }"
                        >
                          <option value="newest" {{ request()->has('sort') && request()->get('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                          <option value="followed" {{ request()->has('sort') && request()->get('sort') == 'followed' ? 'selected' : '' }}>Most Followers</option>
                      </select>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 !gap-3 lg:!gap-6 !mb-6">
                        
                          @foreach ($creators as $creator)
                            <div class="relative group flex flex-col items-stretch justify-start !gap-1 group/card">
                              @include('site.components.favorite.button', [
                                'stroke' => '#FF2C0C',
                                'type' => 'author',
                                'item_id' => $creator->id,
                                'class' => 'absolute !bg-white/50 hover:!bg-white !p-2 !rounded-lg !top-2 !right-2 group-has-[.favorite-active]:!bg-white',
                              ])
                              <x-link href="{{ $creator->makeProfileUrl() }}" class="rounded overflow-hidden !leading-0 !border-none">
                                <img class="object-cover w-full h-full" src="{{ $creator->avatar }}" alt="" class="" />
                              </x-link>

                              <x-link href="{{ $creator->makeProfileUrl() }}" class="flex justify-start items-center !gap-2 !border-0 group-has-[a]/card:!text-black">
                                <div class="">{{ $creator->getName() }}</div>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7.25274 0.330074C7.34748 0.226094 7.46287 0.143029 7.59154 0.0861931C7.72022 0.0293569 7.85933 0 8 0C8.14067 0 8.27978 0.0293569 8.40845 0.0861931C8.53713 0.143029 8.65252 0.226094 8.74726 0.330074L9.32145 0.960878C9.45171 1.10377 9.62022 1.20632 9.807 1.25634C9.99377 1.30637 10.191 1.30178 10.3752 1.24312L11.188 0.984331C11.322 0.941704 11.4634 0.927508 11.6032 0.942649C11.7431 0.95779 11.8782 1.00194 12 1.07226C12.1217 1.14259 12.2275 1.23756 12.3105 1.35109C12.3935 1.46461 12.4519 1.59421 12.4819 1.73159L12.6639 2.56457C12.7052 2.7535 12.7999 2.92661 12.9366 3.06336C13.0734 3.20011 13.2465 3.29477 13.4354 3.3361L14.2684 3.51806C14.4058 3.54812 14.5354 3.60651 14.6489 3.68951C14.7624 3.7725 14.8574 3.87827 14.9277 4.00005C14.9981 4.12183 15.0422 4.25694 15.0574 4.39676C15.0725 4.53657 15.0583 4.678 15.0157 4.81202L14.7569 5.62478C14.6982 5.80903 14.6936 6.00623 14.7437 6.193C14.7937 6.37978 14.8962 6.54829 15.0391 6.67855L15.6699 7.25274C15.7739 7.34748 15.857 7.46287 15.9138 7.59154C15.9706 7.72022 16 7.85933 16 8C16 8.14067 15.9706 8.27978 15.9138 8.40846C15.857 8.53713 15.7739 8.65252 15.6699 8.74726L15.0391 9.32145C14.8962 9.45171 14.7937 9.62022 14.7437 9.807C14.6936 9.99377 14.6982 10.191 14.7569 10.3752L15.0157 11.188C15.0583 11.322 15.0725 11.4634 15.0574 11.6032C15.0422 11.7431 14.9981 11.8782 14.9277 12C14.8574 12.1217 14.7624 12.2275 14.6489 12.3105C14.5354 12.3935 14.4058 12.4519 14.2684 12.4819L13.4354 12.6639C13.2465 12.7052 13.0734 12.7999 12.9366 12.9366C12.7999 13.0734 12.7052 13.2465 12.6639 13.4354L12.4819 14.2684C12.4519 14.4058 12.3935 14.5354 12.3105 14.6489C12.2275 14.7624 12.1217 14.8574 12 14.9277C11.8782 14.9981 11.7431 15.0422 11.6032 15.0574C11.4634 15.0725 11.322 15.0583 11.188 15.0157L10.3752 14.7569C10.191 14.6982 9.99377 14.6936 9.807 14.7437C9.62022 14.7937 9.45171 14.8962 9.32145 15.0391L8.74726 15.6699C8.65252 15.7739 8.53713 15.857 8.40845 15.9138C8.27978 15.9706 8.14067 16 8 16C7.85933 16 7.72022 15.9706 7.59154 15.9138C7.46287 15.857 7.34748 15.7739 7.25274 15.6699L6.67855 15.0391C6.54829 14.8962 6.37978 14.7937 6.193 14.7437C6.00623 14.6936 5.80903 14.6982 5.62478 14.7569L4.81201 15.0157C4.678 15.0583 4.53657 15.0725 4.39676 15.0574C4.25694 15.0422 4.12183 14.9981 4.00005 14.9277C3.87827 14.8574 3.7725 14.7624 3.68951 14.6489C3.60651 14.5354 3.54812 14.4058 3.51806 14.2684L3.3361 13.4354C3.29477 13.2465 3.20011 13.0734 3.06336 12.9366C2.92661 12.7999 2.7535 12.7052 2.56457 12.6639L1.73159 12.4819C1.59421 12.4519 1.46461 12.3935 1.35109 12.3105C1.23756 12.2275 1.14259 12.1217 1.07226 12C1.00194 11.8782 0.95779 11.7431 0.942649 11.6032C0.927508 11.4634 0.941704 11.322 0.984331 11.188L1.24312 10.3752C1.30178 10.191 1.30637 9.99377 1.25634 9.807C1.20632 9.62022 1.10377 9.45171 0.960878 9.32145L0.330074 8.74726C0.226094 8.65252 0.143029 8.53713 0.0861931 8.40846C0.0293569 8.27978 0 8.14067 0 8C0 7.85933 0.0293569 7.72022 0.0861931 7.59154C0.143029 7.46287 0.226094 7.34748 0.330074 7.25274L0.960878 6.67855C1.10377 6.54829 1.20632 6.37978 1.25634 6.193C1.30637 6.00623 1.30178 5.80903 1.24312 5.62478L0.984331 4.81202C0.941704 4.678 0.927508 4.53657 0.942649 4.39676C0.95779 4.25694 1.00194 4.12183 1.07226 4.00005C1.14259 3.87827 1.23756 3.7725 1.35109 3.68951C1.46461 3.60651 1.59421 3.54812 1.73159 3.51806L2.56457 3.3361C2.7535 3.29477 2.92661 3.20011 3.06336 3.06336C3.20011 2.92661 3.29477 2.7535 3.3361 2.56457L3.51806 1.73159C3.54812 1.59421 3.60651 1.46461 3.68951 1.35109C3.7725 1.23756 3.87827 1.14259 4.00005 1.07226C4.12183 1.00194 4.25694 0.95779 4.39676 0.942649C4.53657 0.927508 4.678 0.941704 4.81201 0.984331L5.62478 1.24312C5.80903 1.30178 6.00623 1.30637 6.193 1.25634C6.37978 1.20632 6.54829 1.10377 6.67855 0.960878L7.25274 0.330074Z"
                                        fill="#37B518" />
                                    <path
                                        d="M11.9885 6.36973L10.8449 5.2262L7.21133 8.85979L5.20408 6.85254L4.06055 7.99607L7.21133 11.1469L11.9885 6.36973Z"
                                        fill="white" />
                                </svg>
                              </x-link>

                              <x-link href="{{ $creator->makeProfileUrl() }}" class="!border-none">{{ $creator->profile }}</x-link>

                              <div class="flex justify-start items-center !gap-2 text-gray">
                                  <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                                      xmlns="http://www.w3.org/2000/svg">
                                      <path
                                          d="M9.75091 11.1142C12.1932 11.1079 14.2698 12.2293 15.0331 14.6432C13.4946 15.5811 11.6836 15.9423 9.75091 15.9376C7.81826 15.9423 6.00727 15.5811 4.46875 14.6432C5.23293 12.2267 7.30602 11.1079 9.75091 11.1142Z"
                                          stroke="currentColor" stroke-width="1.5" stroke-linecap="square" />
                                      <circle cx="9.7523" cy="5.3773" r="3.3148" stroke="currentColor" stroke-width="1.5"
                                          stroke-linecap="square" />
                                  </svg>
                                  <p class="">{{ $creator->followers()->count() }} Followers
                                  </p>
                              </div>
                            </div>
                          @endforeach
                        
                    </div>
                    @include('site.components.paginator', ['paginator' => $creators])
                  @endif
                </div>

            </div>
        </div>
    </section>
@endsection


@push('js')
  <script>
      function parseUrlParams(url) {
        let queryString = url ? url.split('?')[1] : window.location.search.slice(1);
        let obj = {};

        if (!queryString) return obj;

        queryString = queryString.split('#')[0];

        const params = queryString.split('&');

        params.forEach(param => {
            let [key, value = 'true'] = param.split('=');
            key = decodeURIComponent(key);
            value = decodeURIComponent(value);

            const arrayMatch = key.match(/(.+)\[(\d*)\]$/);
            if (arrayMatch) {
                const baseKey = arrayMatch[1];
                const index = arrayMatch[2];

                if (!obj[baseKey]) obj[baseKey] = [];

                if (index) {
                    obj[baseKey][parseInt(index)] = value;
                } else {
                    obj[baseKey].push(value);
                }
            } else {
                if (obj[key] === undefined) {
                    obj[key] = value;
                } else if (Array.isArray(obj[key])) {
                    obj[key].push(value);
                } else {
                    obj[key] = [obj[key], value];
                }
            }
        });

        return obj;
      }

      function makeSearchableItem(data) {
          const item = $("<span>", {
            class: 'search-item bg-light rounded !p-1 !text-sm !text-gray flex items-center justify-start !gap-2 group hover:cursor-pointer',
          });
          const remove = $("<a>", {
              href: "#",
              class: "disabled",
          });

          remove.on("click", function (evt) {
              evt.preventDefault();
              $(this).parents("span").detach();
          });

          remove.html(
              '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none"><path d="M3 3.5C5.34315 5.84315 6.65686 7.15685 9 9.5M3 9.5C5.34315 7.15685 6.65686 5.84315 9 3.5" stroke="#A4A0A0" stroke-width="0.5" stroke-linecap="round" /> </svg>'
          );

          item.attr("data-value", data.slug);
          item.text(data.label);
          item.append(remove);
          
          return item;
      }

       $(".accordion")
          .find(".search-input")
          .on("searchItemSelected", function(evt, data) {
              const block = $(this).closest(".accordion");
              const result = block.find(".search_results");
              const item = makeSearchableItem(data);
              
              if (!result.find('span[data-value="' + data.slug + '"]').length) {
                result.append(item);
              }
          });
        
        $('.lang-btn').on('click', function() {
          $(this).toggleClass('bg-active text-white active');
        });

        $('.platform-item').on('click', function() {
          $(this).toggleClass('bg-active text-white active');
        });

        $('#apply-filters').on('click', (evt) => {
          evt.preventDefault();
          const collab = Number($('#collaboration').is(':checked'));
          const search = (new URLSearchParams(window.location.search)).get('q');
          const sort = (new URLSearchParams(window.location.search)).get('sort');
          const formData = {
            followers_min: $('#accordion').find('.followers-min').val(),
            followers_max: $('#accordion').find('.followers-max').val(),
            langs: $('#accordion').find('#lang').find('.search-item').map((k, el) => $(el).data('value')).get(),
            countries: $('#accordion').find('#country').find('.search-item').map((k, el) => $(el).data('value')).get(),
            platforms: $('#accordion').find('#platform').find('.platform-item.active').map((k, el) => $(el).data('value')).get(),
          };

          if (collab) {
            formData['collaboration'] = 1;
          }

          if (search) {
            formData['q'] = search;
          }

          if (sort) {
            formData['sort'] = sort;
          }
          
          const query = objectToQueryString(formData);
          window.location.href = '/creators?' + query;
        });


        const params = parseUrlParams();
        if (Object.keys(params).includes('countries')) {
          const selected_countries = params.countries.map(country => {
            const item = makeSearchableItem({ label: country, slug: country });
            if (item.length) {
              document.querySelector('.country-results').append(item[0]);
            }
          });
        }

        if (Object.keys(params).includes('langs')) {
          const selected_langs = params.langs.map(lang => {
            const item = makeSearchableItem({ label: lang, slug: lang });
            if (item.length) {
              document.querySelector('.language-results').append(item[0]);
            }
          });
        }

        if (Object.keys(params).includes('platforms')) {
          const selected_langs = params.platforms.map(platform => {
            document.querySelector(`div[data-value="${platform}"]`).classList.add('bg-active', 'text-white', 'active');
          });
        }
  </script>
@endpush