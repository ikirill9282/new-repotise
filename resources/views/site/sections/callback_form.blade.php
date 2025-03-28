<section class="get_touch">
    <div class="container">
        <div class="about_block">
            <div class="left_group">
                @include('site.components.heading', ['variables' => $variables])
                <p>{!! $variables->get('subtitle')?->value ?? '' !!}</p>
                <form>
                    <div class="input_block">
                        <input type="text" placeholder="{{ $variables->get('name_placeholder')?->value ?? '' }}">
                        @include('icons.shield_answer')
                    </div>
                    <select>
                        <option value="" disabled selected>{{ $variables->get('subject_placeholder')?->value ?? '' }}</option>
                        <option value="Subject">Subject</option>
                        <option value="Subject1">Subject1</option>
                        <option value="Subject2">Subject2</option>
                    </select>
                    <div class="textarea_block">
                        <textarea placeholder="{!! $variables->get('message_placeholder')?->value ?? '' !!}"></textarea>
                        @include('icons.shield_answer')
                    </div>
                    <a href="#">
                      @include('icons.download')
                    </a>
                    <button>{{ $variables->get('button_message')?->value ?? '' }}</button>
                </form>
            </div>
            <img src="{{ asset('/assets/img/img_touch.png') }}" alt="Form Image">
        </div>
    </div>
</section>
