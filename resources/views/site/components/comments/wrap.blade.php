<div class="about_block">
  <div class="title_block">
      @include('site.components.heading', ['title' => 'comment'])
      <span>{{ $model->countComments() }}</span>
  </div>
  <div class="write_comment_group">
      @if (!auth()->check())
          <a href="#" class="go_comment open_auth">Login to comment</a>
      @elseif (auth()->user()->can('write_comment'))
          <h3 class="comment_mobile_header">{{ auth()->user()->profile }}</h3>
          <div class="write_comment !items-start" id="ta-ct-{{ $model->id }}">
              <h3>{{ auth()->user()->profile }}</h3>
              <textarea class="outline-0 transition comment-input w-full" rows="1" wrap="hard"
                  data-emojibtn="#emoji-btn-{{ $model->id }}"
                  placeholder="{{ trim(print_var('comment_add_message', $variables)) }}" {{ auth()->check() ? '' : 'disabled' }}></textarea>

              <div class="right_stickers">
                  <a href="#"
                      class="numbers pointer-events-none {{ auth()->check() ? '' : 'unlinked' }}">0/1000</a>
                  <button
                      class="relative bg-white rounded !p-[4px] transition emoji-btn first_stick {{ auth()->check() ? '' : 'disabled' }}"
                      id="emoji-btn-{{ $model->id }}">
                      @include('icons.smiles')
                  </button>
                  <a href="#" class="third_stick {{ auth()->check() ? '' : 'disabled' }}"
                      onclick="event.preventDefault()">
                      @include('icons.arrow_right')
                  </a>
              </div>
          </div>
      @endif
  </div>
  <div class="block_commends">
      {{-- @dump($model->comments) --}}
      @foreach ($model->comments as $comment)
          @include('site.components.comments.comment', ['comment' => $comment])
      @endforeach

      <div class="more_commends_group">
          <a href="#">{{ print_var('comment_more_comments', $variables) }} (50 of 248)</a>
      </div>
  </div>
</div>