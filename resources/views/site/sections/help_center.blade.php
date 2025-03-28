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
                        @include('site.components.heading', ['title' => $group])
                        @foreach ($questions_group as $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-{{ $item->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $item->id }}" aria-expanded="true"
                                        aria-controls="collapse-{{ $item->id }}">
                                        {!! $item->text !!}
                                    </button>
                                </h2>
                                <div id="collapse-{{ $item->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample" aria-labelledby="heading-{{ $item->id }}">
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
