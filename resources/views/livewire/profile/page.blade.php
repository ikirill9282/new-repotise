<div>
    @php
      $user = \App\Models\User::find(\Illuminate\Support\Facades\Crypt::decrypt($this->user_id));
      $options = $user?->options;
      $isOwner = auth()->check() && auth()->user()->id === $user->id;
      $creatorVisible = $options->creator_visible ?? true;
      $showDonate = $options->show_donate ?? true;
      $showProducts = $options->show_products ?? true;
      $showInsights = $options->show_insights ?? true;
      $collaborationOpen = (bool) ($options->collaboration ?? false);
    @endphp

    @if(!$creatorVisible && !$isOwner)
      <section class="creatorPage bg-light">
        <div class="container {{ $container }}">
          <div class="max-w-xl mx-auto text-center py-16 space-y-3">
            <h2 class="text-2xl font-semibold">This creator profile is private.</h2>
            <p class="text-gray">
              {{ $user->getName() }} has chosen to keep their seller page hidden for now. Check back later or follow them for updates.
            </p>
            <x-link href="{{ route('creators') }}" class="inline-block">Browse other creators</x-link>
          </div>
        </div>
      </section>
    @else
      <section class="creatorPage bg-light">
        <div class="container {{ $container }}">
            <div class="grid grid-cols-1 md:!gap-3 items-start md:grid-cols-[1fr_1fr_280px] lg:grid-cols-[1fr_1fr_380px]">
                <div class="order-2 md:!order-1 col-span-2">
                  @if(!$creatorVisible && $isOwner)
                    <x-card size="sm" class="!mb-3 !border-amber-300 !bg-amber-50">
                      <p class="text-sm text-amber-700">
                        Your seller page is currently hidden from visitors. Toggle on “Creator Page Visibility” in Seller Settings to publish it.
                      </p>
                    </x-card>
                  @endif

                  {{-- PROFILE --}}
                  <x-card size="sm" class="!mb-3">
                    <div class="creatorPage__content-author author-page-content">
                      <div class="flex justify-start items-start gap-2 sm:gap-4 !text-sm sm:!text-base mb-4">
                          <div class="!w-14 !h-14 sm:!w-18 sm:!h-18 rounded-full overflow-hidden">
                            <img class="object-cover" src="{{ $user->avatar }}" alt="Avatart"
                              class="creatorPage__content-author-avatar" />
                          </div>
                          <div class="">
                              <div class="flex items-center gap-2 sm:mb-2 lg:mb-3">
                                  <p class="">{{ $user->getName() }}</p>
                                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                      xmlns="http://www.w3.org/2000/svg">
                                      <path
                                          d="M7.25274 0.330074C7.34748 0.226094 7.46287 0.143029 7.59154 0.0861931C7.72022 0.0293569 7.85933 0 8 0C8.14067 0 8.27978 0.0293569 8.40845 0.0861931C8.53713 0.143029 8.65252 0.226094 8.74726 0.330074L9.32145 0.960878C9.45171 1.10377 9.62022 1.20632 9.807 1.25634C9.99377 1.30637 10.191 1.30178 10.3752 1.24312L11.188 0.984331C11.322 0.941704 11.4634 0.927508 11.6032 0.942649C11.7431 0.95779 11.8782 1.00194 12 1.07226C12.1217 1.14259 12.2275 1.23756 12.3105 1.35109C12.3935 1.46461 12.4519 1.59421 12.4819 1.73159L12.6639 2.56457C12.7052 2.7535 12.7999 2.92661 12.9366 3.06336C13.0734 3.20011 13.2465 3.29477 13.4354 3.3361L14.2684 3.51806C14.4058 3.54812 14.5354 3.60651 14.6489 3.68951C14.7624 3.7725 14.8574 3.87827 14.9277 4.00005C14.9981 4.12183 15.0422 4.25694 15.0574 4.39676C15.0725 4.53657 15.0583 4.678 15.0157 4.81202L14.7569 5.62478C14.6982 5.80903 14.6936 6.00623 14.7437 6.193C14.7937 6.37978 14.8962 6.54829 15.0391 6.67855L15.6699 7.25274C15.7739 7.34748 15.857 7.46287 15.9138 7.59154C15.9706 7.72022 16 7.85933 16 8C16 8.14067 15.9706 8.27978 15.9138 8.40846C15.857 8.53713 15.7739 8.65252 15.6699 8.74726L15.0391 9.32145C14.8962 9.45171 14.7937 9.62022 14.7437 9.807C14.6936 9.99377 14.6982 10.191 14.7569 10.3752L15.0157 11.188C15.0583 11.322 15.0725 11.4634 15.0574 11.6032C15.0422 11.7431 14.9981 11.8782 14.9277 12C14.8574 12.1217 14.7624 12.2275 14.6489 12.3105C14.5354 12.3935 14.4058 12.4519 14.2684 12.4819L13.4354 12.6639C13.2465 12.7052 13.0734 12.7999 12.9366 12.9366C12.7999 13.0734 12.7052 13.2465 12.6639 13.4354L12.4819 14.2684C12.4519 14.4058 12.3935 14.5354 12.3105 14.6489C12.2275 14.7624 12.1217 14.8574 12 14.9277C11.8782 14.9981 11.7431 15.0422 11.6032 15.0574C11.4634 15.0725 11.322 15.0583 11.188 15.0157L10.3752 14.7569C10.191 14.6982 9.99377 14.6936 9.807 14.7437C9.62022 14.7937 9.45171 14.8962 9.32145 15.0391L8.74726 15.6699C8.65252 15.7739 8.53713 15.857 8.40845 15.9138C8.27978 15.9706 8.14067 16 8 16C7.85933 16 7.72022 15.9706 7.59154 15.9138C7.46287 15.857 7.34748 15.7739 7.25274 15.6699L6.67855 15.0391C6.54829 14.8962 6.37978 14.7937 6.193 14.7437C6.00623 14.6936 5.80903 14.6982 5.62478 14.7569L4.81201 15.0157C4.678 15.0583 4.53657 15.0725 4.39676 15.0574C4.25694 15.0422 4.12183 14.9981 4.00005 14.9277C3.87827 14.8574 3.7725 14.7624 3.68951 14.6489C3.60651 14.5354 3.54812 14.4058 3.51806 14.2684L3.3361 13.4354C3.29477 13.2465 3.20011 13.0734 3.06336 12.9366C2.92661 12.7999 2.7535 12.7052 2.56457 12.6639L1.73159 12.4819C1.59421 12.4519 1.46461 12.3935 1.35109 12.3105C1.23756 12.2275 1.14259 12.1217 1.07226 12C1.00194 11.8782 0.95779 11.7431 0.942649 11.6032C0.927508 11.4634 0.941704 11.322 0.984331 11.188L1.24312 10.3752C1.30178 10.191 1.30637 9.99377 1.25634 9.807C1.20632 9.62022 1.10377 9.45171 0.960878 9.32145L0.330074 8.74726C0.226094 8.65252 0.143029 8.53713 0.0861931 8.40846C0.0293569 8.27978 0 8.14067 0 8C0 7.85933 0.0293569 7.72022 0.0861931 7.59154C0.143029 7.46287 0.226094 7.34748 0.330074 7.25274L0.960878 6.67855C1.10377 6.54829 1.20632 6.37978 1.25634 6.193C1.30637 6.00623 1.30178 5.80903 1.24312 5.62478L0.984331 4.81202C0.941704 4.678 0.927508 4.53657 0.942649 4.39676C0.95779 4.25694 1.00194 4.12183 1.07226 4.00005C1.14259 3.87827 1.23756 3.7725 1.35109 3.68951C1.46461 3.60651 1.59421 3.54812 1.73159 3.51806L2.56457 3.3361C2.7535 3.29477 2.92661 3.20011 3.06336 3.06336C3.20011 2.92661 3.29477 2.7535 3.3361 2.56457L3.51806 1.73159C3.54812 1.59421 3.60651 1.46461 3.68951 1.35109C3.7725 1.23756 3.87827 1.14259 4.00005 1.07226C4.12183 1.00194 4.25694 0.95779 4.39676 0.942649C4.53657 0.927508 4.678 0.941704 4.81201 0.984331L5.62478 1.24312C5.80903 1.30178 6.00623 1.30637 6.193 1.25634C6.37978 1.20632 6.54829 1.10377 6.67855 0.960878L7.25274 0.330074Z"
                                          fill="#37B518" />
                                      <path
                                          d="M11.9904 6.36967L10.8469 5.22614L7.21328 8.85973L5.20603 6.85248L4.0625 7.99601L7.21328 11.1468L11.9904 6.36967Z"
                                          fill="white" />
                                  </svg>
                              </div>
                              <p class="">
                    <span class="text-gray">Open for Collaboration:</span>
                                  <span>{{ $collaborationOpen ? 'Yes' : 'No' }}</span>
                              </p>
                              <p class="">
                                  <span class="text-gray">Country:</span>
                                  <span>{{ $user->country }}</span>
                              </p>
                          </div>
                      </div>
                      <div class="creatorPage__content-infoBlok-list">
                          @if($user->options->page_banner)
                            <div class="h-48 lg:h-65 mb-4 rounded-lg overflow-hidden">
                              <img class="object-cover h-full w-full" src="{{ $user->options->page_banner }}" alt="Banner">
                            </div>
                          @endif
                          <div class="">
                            {!! $user->options->description !!}
                          </div>
                      </div>
                    </div>
                  </x-card>
                  
                  {{-- PRODUCTS --}}
                  @if($showProducts || $isOwner)
                  <x-card size="sm" class="!mb-3">
                    @if(!$showProducts && $isOwner)
                      <div class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded px-3 py-2 mb-4">
                        Hidden for visitors. Enable “Show ‘Products’ Section” in Seller Settings to make it visible.
                      </div>
                    @endif
                    <h4 class="creatorPage__content-title !mb-8">
                        @if($user->products()->exists())
                          <span>Products</span>
                        @else
                          <span>Potential Products</span>
                        @endif
                        <span class="text-gray">({{ $user->products()->count() }})</span>
                    </h4>

                    @if($user->products()->exists())
                      <div class="">
                        <x-product.slider 
                          :products="$user->products()->where('status_id', \App\Enums\Status::ACTIVE)->whereNotNull('published_at')->latest()->limit(6)->get()"
                          id="profile-product-slider"
                        ></x-product.slider>
                      </div>

                      <div class="creatorPage__content-products-moreBtn-wrapper">
                          <x-link class="select-none" href="{{ route('products') . '?author=' . $user->profile }}">Show More</x-link>
                      </div>
                    @else
                      <div class="max-w-lg text-center mx-auto">
                        <b>{{ $user->getName() }}</b>'s travel products will be listed here once this profile is claimed on TrekGuider. Check back for unique guides and maps!
                      </div>
                    @endif
                  </x-card>
                  @endif

                  {{-- ARTICLES --}}
                  @if($showInsights || $isOwner)
                  <x-card size="sm" >
                    @if(!$showInsights && $isOwner)
                      <div class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded px-3 py-2 mb-4">
                        Hidden for visitors. Enable “Show ‘Travel Insights’” in Seller Settings to make it visible.
                      </div>
                    @endif
                    <h4 class="!mb-8">
                        @if($user->articles()->exists())
                          <span>Travel Insights</span>
                        @else
                          <span>Potential Travel Insights</span>
                        @endif
                        <span class="text-gray">({{ $user->articles()->count() }})</span>
                    </h4>

                    @if ($user->articles()->exists())
                      <div class="w-full">
                          @foreach ($user->articles()->latest()->limit(6)->get() as $article)
                            <div class="flex flex-col mb-10 last:mb-0">
                                <div class="flex justify-start items-center mb-2">
                                    <a href="{{ $article->author->makeProfileUrl() }}" class="w-9 h-9 mr-1 rounded-full overflow-hidden hover:opacity-80 transition">
                                      <img src="{{ $article->author->avatar }}" alt="Avatar"
                                        class="object-cover w-full h-full" />
                                    </a>
                                    <div class="text-gray text-sm !leading-5">
                                      <a href="{{ $article->author->makeProfileUrl() }}" class="hover:!text-black transition !text-inherit">
                                        <p>{{ $article->author->getName() }}</p>
                                      </a>
                                      <a href="{{ $article->author->makeProfileUrl() }}" class="hover:!text-black transition !text-inherit">
                                        <p>{{ $article->author->profile }}</p>
                                      </a>
                                    </div>
                                    @if(auth()->user()?->id == $article->author->id)
                                      <x-btn href="{{ $article->makeEditUrl() }}" class="!flex items-center sm:!text-sm !w-auto gap-2 !px-4 !py-1 !ml-3">
                                        <span>@include('icons.edit')</span>
                                        <span>Edit Insights</span>
                                      </x-btn>
                                    @else
                                      @if(!$user->hasFollower(auth()->user()?->id))
                                        <x-btn 
                                          href="{{ $user->makeSubscribeUrl() }}" 
                                          class="follow follow-btn sm:!text-sm !w-auto gap-2 !px-4 !py-1 !ml-3 {{ auth()->check() ? '' : 'open_auth' }}"
                                          data-resource="{{ \Illuminate\Support\Facades\Crypt::encrypt($user->id) }}"
                                          data-group="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $user->id]); }}"
                                        >
                                          Subscribe
                                        </x-btn>
                                      @endif
                                    @endif
                                </div>
                                <div class="flex justify-start items-center gap-2 text-sm mb-4">
                                    <p class="text-gray bg-light px-2 py-1 rounded-full">{{ \Illuminate\Support\Carbon::parse($article->created_at)->format('d.m.Y') }}</p>
                                    <p class="text-gray bg-light px-2 py-1 rounded-full">{{ $article->views }} Views</p>
                                </div>
                                <div class="mb-4">
                                    <a href="{{ $article->makeFeedUrl() }}" class="block hover:opacity-80 transition !text-inherit hover:!text-black">
                                        <h6 class="!text-2xl mb-4">{{ $article->title }}</h6>
                                    </a>
                                    <a href="{{ $article->makeFeedUrl() }}" class="block text-gray read-more read-more-300 hover:opacity-80 transition !text-inherit hover:!text-black">
                                        {!! $article->getText() !!}
                                    </a>
                                </div>
                                <div class="flex items-center justify-start flex-wrap gap-1">
                                    @foreach ($article->tags as $tag)  
                                      <a href="{{ route('search') . '?q=' . $tag->title }}" class="px-2.5 py-1 text-sm rounded-full transition
                                              !text-gray !bg-light hover:!bg-second hover:!text-light hover:cursor-pointer"
                                        >
                                          {{ $tag->title }}
                                        </a>
                                    @endforeach
                                </div>
                                <div class="flex justify-start items-center !gap-1.5 sm:!gap-3 mt-4">
                                    <div class="">
                                        @foreach($article->likes()->latest()->limit(3)->get() as $like)
                                          <a href="{{ $like->author->makeProfileUrl() }}" class="!w-5 !h-5 inline-block rounded-full overflow-hidden shadow-sm ml-[-8px] first:!ml-0">
                                              <img class="object-cover w-full h-full" src="{{ $like->author->avatar }}" alt="" />
                                          </a>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-start items-center gap-1.5 sm:gap-3">
                                        @php
                                          $hash_id = \App\Helpers\CustomEncrypt::generateUrlHash([$article->id]);
                                        @endphp

                                        <x-like :id="$article->id" :count="$article->likes()->count()"></x-like>

                                        <div class="flex justify-start items-center gap-1 text-sm">
                                            <a href="{{ auth()->user()?->makeReferalArticleUrl('FB', $article) ?? $article->makeShareUrl('FB') }}" target="_blank" class="first_connect !text-gray hover:!text-blue-500">
                                              @include('icons.facebook-sm')
                                            </a>
                                            <a href="{{ auth()->user()?->makeReferalArticleUrl('TW', $article) ?? $article->makeShareUrl('TW') }}" target="_blank" class="second_connect !text-gray hover:!text-black">
                                              @include('icons.twitter-sm')
                                            </a>
                                            <a href="{{ auth()->user()?->makeReferalArticleUrl('RD', $article) ?? $article->makeShareUrl('RD') }}" target="_blank" class="third_connect !text-gray hover:!text-black">
                                              @include('icons.reddit-sm')
                                            </a>
                                            <x-link href="{{ auth()->user()?->makeReferalArticleUrl(null, $article) ?? $article->makeShareUrl() }}" class="share copyToClipboard ml-1" data-target="{{ $hash_id }}">
                                              <input data-copyId="{{ $hash_id }}" type="hidden" value="{{ auth()->user()?->makeReferalArticleUrl(null, $article) ?? $article->makeShareUrl() }}"></input>
                                              Share
                                            </x-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          @endforeach
                      </div>
                      <div class="text-center mt-10">
                        <x-link class="select-none" href="{{ route('insights') . '?author=' . $user->profile }}">Show More</x-link>
                      </div>
                    @else
                      <div class="max-w-lg text-center mx-auto">
                        Travel insights and tips from <b>{{ $user->getName() }}</b> will be published here once this profile is claimed. Get ready for inspiring content!
                      </div>
                    @endif
                  </x-card>
                  @endif
                </div>


                <aside class="flex flex-col gap-4 order-1 md:!order-2 
                  top-[80px] rightt-0 col-span-1 bg-white !p-2 sm:!p-4 rounded">

                    @if(auth()->user()?->id == $user->id && auth()->user()?->hasRole('creator'))
                      <x-btn href="{{ route('profile.edit') }}" class="!py-2 !max-w-none">Edit Profile</x-btn>
                    @else
                      <x-btn 
                        href="{{ $user->makeSubscribeUrl() }}" 
                        class="follow follow-btn !w-auto !py-2 {{ auth()->check() ? '' : 'open_auth' }}"
                        data-resource="{{ \Illuminate\Support\Facades\Crypt::encrypt($user->id) }}"
                        data-group="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $user->id]); }}"
                      >
                        {{ $user->hasFollower(auth()->user()?->id) ? 'Unsubscribe' : 'Subscribe' }}
                      </x-btn>
                    @endif

                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-start gap-1">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.00098 10.1155C10.8934 10.1155 13.335 10.5864 13.335 12.3997C13.3346 14.2134 10.8772 14.6663 8.00098 14.6663C5.10937 14.6663 2.66797 14.1964 2.66797 12.3831C2.66812 10.5693 5.1248 10.1156 8.00098 10.1155ZM8.00098 1.33331C9.96037 1.33331 11.5303 2.90265 11.5303 4.86066C11.5303 6.81868 9.96038 8.38898 8.00098 8.38898C6.04239 8.3888 4.47168 6.81857 4.47168 4.86066C4.47169 2.90276 6.0424 1.33349 8.00098 1.33331Z"
                                    fill="#A4A0A0" />
                            </svg>
                            <p class="">
                                <span class="text-gray">Social Followers:</span>
                                <span>13 000</span>
                            </p>
                        </div>
                        <div class="flex items-center justify-start gap-1">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8.00098 10.1155C10.8934 10.1155 13.335 10.5864 13.335 12.3997C13.3346 14.2134 10.8772 14.6663 8.00098 14.6663C5.10937 14.6663 2.66797 14.1964 2.66797 12.3831C2.66812 10.5693 5.1248 10.1156 8.00098 10.1155ZM8.00098 1.33331C9.96037 1.33331 11.5303 2.90265 11.5303 4.86066C11.5303 6.81868 9.96038 8.38898 8.00098 8.38898C6.04239 8.3888 4.47168 6.81857 4.47168 4.86066C4.47169 2.90276 6.0424 1.33349 8.00098 1.33331Z"
                                    fill="#A4A0A0" />
                            </svg>
                            <p class="">
                                <span class="text-gray">TrekGuider Followers:</span>
                                <span>{{ $user->followers()->count() }}</span>
                            </p>
                        </div>
                        <div class="flex items-center justify-start gap-1">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4.94953 12.6048C5.50231 12.6048 5.9552 13.0677 5.95539 13.639C5.95539 14.2037 5.50243 14.6664 4.94953 14.6664C4.39013 14.6662 3.93781 14.2036 3.93781 13.639C3.938 13.0678 4.39024 12.605 4.94953 12.6048ZM12.4437 12.6048C12.9965 12.6048 13.4493 13.0677 13.4495 13.639C13.4495 14.2037 12.9966 14.6664 12.4437 14.6664C11.8843 14.6661 11.4319 14.2036 11.4319 13.639C11.4321 13.0678 11.8844 12.605 12.4437 12.6048ZM1.91925 1.3392L3.50812 1.58334C3.73446 1.62495 3.90123 1.81502 3.92121 2.04623L4.04718 3.56967C4.06716 3.78796 4.24041 3.95135 4.45343 3.95151H13.4505C13.8565 3.95161 14.1227 4.09481 14.389 4.40756C14.6554 4.72052 14.7028 5.16996 14.6429 5.57748L14.0101 10.0404C13.8901 10.8981 13.1703 11.5295 12.3245 11.5296H5.05695C4.17102 11.5296 3.43791 10.8368 3.36457 9.93881L2.75129 2.52182L1.74543 2.34506C1.47921 2.29721 1.29257 2.03208 1.33918 1.7601C1.38587 1.48192 1.64618 1.29771 1.91925 1.3392ZM9.41339 6.46811C9.13361 6.46811 8.91339 6.69311 8.91339 6.97885C8.91358 7.25763 9.13373 7.48862 9.41339 7.48862H11.2581C11.5378 7.48862 11.7579 7.25763 11.7581 6.97885C11.7581 6.69311 11.5379 6.46811 11.2581 6.46811H9.41339Z"
                                    fill="#A4A0A0" />
                            </svg>
                            <p class="">
                                <span class="text-gray">Products:</span>
                                <span>{{ $user->products()->count() }}</span>
                            </p>
                        </div>
                        <div class="flex items-center justify-start gap-1">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.7939 1.33331C12.8532 1.33331 13.9999 2.51987 14 4.55304V11.4398C14 13.5064 12.8533 14.6663 10.7939 14.6663H5.20703C3.18036 14.6663 2 13.5064 2 11.4398V4.55304C2.00011 2.51987 3.18046 1.33331 5.20703 1.33331H10.7939ZM5.38672 10.4935C5.18687 10.4735 4.99342 10.5665 4.88672 10.7396C4.78009 10.9062 4.78018 11.1268 4.88672 11.3001C4.99343 11.4666 5.18685 11.566 5.38672 11.5394H10.6133C10.8792 11.5127 11.08 11.2858 11.0801 11.0198C11.08 10.7466 10.8792 10.5201 10.6133 10.4935H5.38672ZM5.38672 7.45245C5.09952 7.45248 4.8664 7.68647 4.86621 7.97296C4.86621 8.25961 5.0994 8.49345 5.38672 8.49347H10.6133C10.8999 8.49347 11.1328 8.25963 11.1328 7.97296C11.1326 7.68646 10.8998 7.45245 10.6133 7.45245H5.38672ZM5.38672 4.43292V4.43976C5.09963 4.43978 4.86658 4.67295 4.86621 4.95929C4.86621 5.24594 5.0994 5.47978 5.38672 5.4798H7.37891C7.66622 5.4798 7.90035 5.24642 7.90039 4.95245C7.90025 4.66657 7.66615 4.43292 7.37891 4.43292H5.38672Z"
                                    fill="#A4A0A0" />
                            </svg>
                            <p class="">
                                <span class="text-gray">Insights:</span>
                                <span>{{ $user->articles()->count() }}</span>
                            </p>
                        </div>
                    </div>
                    
                    @if(false)
                      {{-- Donation feature temporarily disabled site-wide. --}}
                    @endif

                    {{-- SOCIALS --}}
                    @php
                      $socials = $options->getSocial() ?? [];
                      $visibility = $options->getSocialVisibility() ?? [];
                      $icons = \App\Models\UserOptions::getSocialIcons();
                      $hasVisibleSocials = collect($socials)
                        ->filter(fn ($url, $key) => array_key_exists($key, $icons))
                        ->filter(fn ($url) => !empty($url))
                        ->filter(fn ($url, $key) => (bool) ($visibility[$key] ?? false))
                        ->isNotEmpty();
                    @endphp
                    @if($hasVisibleSocials)
                    <div class="">
                        <h5 class="mb-3">Connect Online</h5>

                        @livewire('profile.social-aside', [
                          'userId' => $user->id,
                          'owner' => $isOwner,
                        ], key('profile-social-aside-' . $user->id))

                        @if(auth()->user()?->id == $user->id)
                          <x-link wire:click.prevent="$dispatch('openModal', { modalName: 'social', args: { user_id: '{{ $this->user_id }}' } })" class="inline-block !mt-3">Add Social Link</x-link>
                        @endif
                    </div>
                    @endif
                    
                    
                    @if($collaborationOpen || $isOwner)
                      <x-btn 
                        wire:click.prevent="$dispatch('openModal', { 
                          modalName: '{{ (auth()->check() && auth()->user()?->id == $user->id) ? 'edit-contacts' : 'contact' }}',
                          args: { user_id: '{{ \Illuminate\Support\Facades\Crypt::encrypt($user->id) }}' }
                        })"
                        class="!py-2 !max-w-none"
                      >
                        Contact Creator
                      </x-btn>
                    @endif
                  
                </aside>
            </div>
        </div>
      </section>
    @endif
</div>

@script
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      Livewire.hook('morphed', () => window.ReadMoreButtons.discover());
    });
  </script>
@endscript
