@php
    $questions = \App\Models\FAQ::where('type', 'question')->with('answer')->get()->groupBy('group');
@endphp


<section class="home_help">
    <div class="container">
        <div class="about_block">
            @include('site.components.heading', ['variables' => $variables])
            @include('site.components.breadcrumbs')
        </div>
    </div>
</section>

<section class="general_questions">
    <div class="container">
        <div class="about_block">
            <div class="questions_group">
                <div class="accordion" id="accordionExample">
                    @foreach ($questions as $group => $questions_group)
                        @php
                            $header_data = [
                                'heading' => '',
                                'header' => '',
                            ];
                            $heading = $variables
                                ->filter(fn($item, $key) => str_contains($key, $group))
                                ->map(function ($item, $key) use (&$header_data) {
                                    foreach ($header_data as $k => $value) {
                                        if (str_contains($key, $k)) {
                                            $header_data[$k] = $item;
                                        }
                                    }
                                });
                        @endphp
                        <{{ $header_data['heading']?->value ?? '' }}>
                            {{ $header_data['header']?->value ?? '' }}
                            </{{ $header_data['heading']?->value ?? '' }}>
                            @foreach ($questions_group as $item)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $item->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $item->id }}" aria-expanded="true" aria-controls="collapse-{{ $item->id }}">
                                            {!! $item->text !!}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $item->id }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionExample"
                                        aria-labelledby="heading-{{ $item->id }}" 
                                        >
                                        <div class="accordion-body">
                                            <div class="text_answers">
                                              {!! $item->answer->text !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                    @endforeach
                </div>
            </div>
            @include('site.components.last_news', ['news' => \App\Models\News::getLastNews()])
        </div>
    </div>
</section>
