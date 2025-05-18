<div class="about_block">
  <div class="title_block">
      @include('site.components.heading', ['variables' => $variables->filter(fn($item) => str_contains($item->name, 'comment'))])
      <span>{{ $model->countComments() }}</span>
  </div>
  <div class="reply-block italic flex justify-start items-stretch !p-4 pr-8 rounded-xl bg-sky-600/5 !mb-5 relative hidden">
    <div class="text-gray-400 rotate-180 !mr-4">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="25px" height="25px" viewBox="0 0 48 48" enable-background="new 0 0 48 48" id="Layer_5" version="1.1" xml:space="preserve">
        <g>
          <polygon fill="currentColor" points="15.706,15.353 15.706,11.251 0.021,23.45 15.706,35.649 15.706,31.548 5.294,23.45  "/>
          <path d="M47.979,29.074c0-6.212-5.038-11.25-11.251-11.25h-0.001H25.479v-6.573L9.794,23.45l15.686,12.199v-6.575   h14.232c3.106,0,5.625,2.52,5.625,5.625c0,0.725-0.148,1.413-0.399,2.05C46.819,34.739,47.979,32.045,47.979,29.074z" fill="currentColor"/>
        </g>
      </svg>
    </div>
    <div class="reply-text grow"></div>
    <div 
      class="text-gray-400 drop-reply absolute !p-4 top-50 right-0 translate-y-[-50%] hover:cursor-pointer"
      data-key="{{ \App\Helpers\CustomEncrypt::generateStaticUrlHas(['id' => $model->id]) }}"
      >
      @include('icons.close')
    </div>
  </div>
  <div class="write_comment_group">
      @if (!auth()->check())
          <a href="#" class="go_comment open_auth">Login to comment</a>
      @elseif (auth()->user()->can('write_comment'))
          <h3 class="comment_mobile_header">{{ auth()->user()->profile }}</h3>

          <form class="feedback-form write_comment !items-start" id="ta-ct-{{ $model->id }}">

              <input type="hidden" name="reply" value="" class="reply-input">
              <input type="hidden" name="article" value="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $model->id]) }}">
            
              <h3>{{ auth()->user()->profile }}</h3>
              <textarea class="outline-0 transition comment-input w-full" name="text" rows="1" wrap="hard"
                  data-emojibtn="#emoji-btn-{{ $model->id }}"
                  placeholder="{{ trim(print_var('comment_add_message', $variables)) }}" {{ auth()->check() ? '' : 'disabled' }}></textarea>

              <div class="right_stickers">
                  <a href="#"
                      class="numbers pointer-events-none {{ auth()->check() ? '' : 'unlinked' }}">0/1000</a>
                  <button
                      type="button"
                      class="relative bg-white rounded !p-[4px] transition emoji-btn first_stick {{ auth()->check() ? '' : 'disabled' }}"
                      id="emoji-btn-{{ $model->id }}">
                      @include('icons.smiles')
                  </button>
                  <button href="#" type="submit" class="bg-white !p-[4px] third_stick {{ auth()->check() ? '' : 'disabled' }}">
                      @include('icons.arrow_right')
                  </button>
              </div>
          </form>
      @endif
  </div>
  <div class="block_commends">
      {{-- @dump($model->comments) --}}
      @foreach ($model->comments as $comment)
          @include('site.components.comments.comment', ['comment' => $comment])
      @endforeach

      @if(isset($comments_count))
        <div class="more_commends_group">
            <a href="#">{{ print_var('comment_more_comments', $variables) }} (50 of {{ $comments_count }})</a>
        </div>
      @endif
  </div>
</div>