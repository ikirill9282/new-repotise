<section class="results_seach">
    <div class="container">
        @if (isset($search_results) && is_array($search_results) && !empty($search_results))
            <div class="about_block">
                <div class="right_select">
                    <span>Sort by:</span>
                    <select>
                        <option>Relevance</option>
                        <option>Relevance1</option>
                        <option>Relevance2</option>
                    </select>
                </div>
                <div class="result_cards">
                    @foreach ($search_results as $item)
                        @switch($item['index'])
                            @case('products')
                              <div class="card_group">
                                  <div class="left_title">
                                      <img src="{{ asset('assets/img/basket_search.svg') }}" alt="Product icon">
                                      <p>Product</p>
                                  </div>
                                  <div class="profile_commendor">
                                      <img src="{{ url(print_key('preview', $item)) }}" alt="" class="img_profile">
                                      <div class="right_text">
                                          <h3>{!! print_key('title', $item) !!}</h3>
                                          <div class="profile">
                                              <img src="{{ print_key('avatar', $item['author']) }}" alt="Avatar {{ print_key('profile', $item['author']) }}">
                                              <p>{{ print_key('profile', $item['author']) }}</p>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="right_group_for_mob">
                                      <div class="left_title mob">
                                          <img src="{{ asset('assets/img/basket_search.svg') }}" alt="Product icon">
                                          <p>Product</p>
                                      </div>
                                      <div class="right_reviews_group">
                                          <div class="cost">
                                              <p>${{ print_key('price', $item) }}</p>
                                              <span>${{ print_key('old_price', $item) }}</span>
                                          </div>
                                          <div class="reviews">
                                              <div class="stars flex">
                                                {{-- TODO: render stars --}}
                                                @foreach (rating_images(print_key('rating', $item) ?? 0) as $star)
                                                  <span><img src="{{ $star }}" alt=""></span>
                                                @endforeach
                                              </div>
                                              <h5>{{ print_key('reviews_count', $item) }} Reviews</h5>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            @break

                            @case('users')
                              <div class="card_group">
                                  <div class="left_title">
                                      <img src="{{ asset('assets/img/profile_search.svg') }}" alt="Profile icon">
                                      <p>Creator</p>
                                  </div>
                                  <div class="profile_commendor">
                                      <img src="{{ url(print_key('avatar', $item)) }}" alt="Profile" class="img_profile">
                                      <div class="right_text">
                                          <h3>{{ print_key('profile', $item) }}</h3>
                                          <div>
                                            {!! print_key('description', $item) !!}
                                          </div>
                                      </div>
                                  </div>
                                  <div class="right_group_for_mob">
                                      <div class="left_title mob">
                                          <img src="{{ asset('assets/img/profile_search.svg') }}" alt="Profile icon">
                                          <p>Creator</p>
                                      </div>
                                      <div class="right_reviews_group">
                                          <div class="reviews">
                                              <h5>
                                                <img src="{{ asset('assets/img/followers.svg') }}" alt="Followers">
                                                {{ print_key('followers_count', $item) }} Followers
                                              </h5>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            @break

                            @case('articles')
                              <div class="card_group">
                                  <div class="left_title">
                                      <img src="{{ asset('assets/img/document_search.svg') }}" alt="Insight icon">
                                      <p>Travel <br>
                                          Insight</p>
                                  </div>
                                  <div class="profile_commendor">
                                      <img src="{{ url(print_key('preview', $item)) }}" alt="Insight" class="img_profile">
                                      <div class="right_text">
                                          <h3>{{ print_key('title', $item) }}</h3>
                                          <div>
                                            {!! print_key('short', $item) !!}
                                          </div>
                                          <div class="profile">
                                              <img src="{{ url(print_key('avatar', $item['author'] ?? [])) }}" alt="Avatar">
                                              <p>{{ print_key('profile', $item['author'] ?? []) }}</p>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="right_group_for_mob">
                                      <div class="left_title mob">
                                          <img src="{{ asset('assets/img/document_search.svg') }}" alt="Insight icon">
                                          <p>Travel <br>
                                              Insight</p>
                                      </div>
                                      <div class="right_reviews_group">
                                          <span class="date">{{ \Illuminate\Support\Carbon::parse(print_key('created_at', $item))->format('d.m.Y') }}</span>
                                      </div>
                                  </div>
                              </div>
                            @break

                          @endswitch
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
