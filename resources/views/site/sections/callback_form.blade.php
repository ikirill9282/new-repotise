<section class="get_touch">
    <div class="container">
        <div class="about_block">
            <div class="left_group">
                @include('site.components.heading', ['variables' => $variables])
                <p>{!! print_var('subtitle', $variables) !!}</p>
                <form>
                    <div class="input_block">
                        <input type="text" placeholder="{{ print_var('name_placeholder', $variables) }}">
                        @include('icons.shield_answer')
                    </div>
                    <select>
                        <option value="" disabled selected>{{ print_var('subject_placeholder', $variables) }}
                        </option>
                        <option value="Subject">Subject</option>
                        <option value="Subject1">Subject1</option>
                        <option value="Subject2">Subject2</option>
                    </select>
                    <div class="textarea_block">
                        <textarea placeholder="{!! print_var('message_placeholder', $variables) !!}"></textarea>
                        @include('icons.shield_answer')
                    </div>
                    <a href="#">
                        @include('icons.download')
                    </a>
                    <button>{{ print_var('button_message', $variables) }}</button>
                </form>
            </div>
            <img src="{{ asset('/assets/img/img_touch.png') }}" alt="Form Image">
        </div>
    </div>
</section>
