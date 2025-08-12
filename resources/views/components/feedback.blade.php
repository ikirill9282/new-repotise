@props([
  'model' => null,
  'type' => null,
  'variables' => collect([])
])

<h3 class="comment_mobile_header">
  {{ auth()->user()->profile }}
</h3>

{{ $head ?? '' }}

<form class="feedback-form write_comment !items-start" id="ta-ct-{{ $model->id }}" data-type="{{ $type }}">
    <input type="hidden" name="reply" value="" class="reply-input">
    <input type="hidden" name="model"
        value="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $model->id]) }}">

    <h3>{{ auth()->user()->profile }}</h3>
    <textarea class="outline-0 transition comment-input w-full" name="text" rows="1" wrap="hard"
        data-emojibtn="#emoji-btn-{{ $model->id }}"
        placeholder="{{ trim(print_var('comment_add_message', $variables)) }}" {{ auth()->check() ? '' : 'disabled' }}></textarea>


    <div class="right_stickers">
        <a href="#"
            class="numbers pointer-events-none {{ auth()->check() ? '' : 'unlinked' }}">0/1000</a>
        <button type="button"
            class="relative bg-white rounded !p-[4px] transition emoji-btn first_stick {{ auth()->check() ? '' : 'disabled' }}"
            id="emoji-btn-{{ $model->id }}">
            @include('icons.smiles')
        </button>
        <button href="#" type="submit"
            class="bg-white !p-[4px] third_stick {{ auth()->check() ? '' : 'disabled' }}">
            @include('icons.arrow_right')
        </button>
    </div>
</form>