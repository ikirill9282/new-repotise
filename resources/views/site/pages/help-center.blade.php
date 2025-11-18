@extends('layouts.site')

@section('content')
    @php
        $variables = $page->variables;
        $questions = \App\Models\FAQ::where('type', 'question')
          ->with('answer')
          ->get()
          ->groupBy('group')
          ->map(fn($group) => $group->map(function($item) {
            return ['title' => $item->text, 'text' => $item->answer->text];
          }))
          ;
        // dd($questions);
    @endphp


    {{-- HERO --}}
    <section class="home_help relative">
        @include('site.components.parallax', ['class' => 'parallax-help'])
        <div class="container !mx-auto relative z-40">
            <div class="about_block">
                @include('site.components.heading', ['variables' => $variables->filter(fn($item) => str_contains($item->name, 'page'))])
                @include('site.components.breadcrumbs')
            </div>
        </div>
    </section>

    {{-- QUESTIONS --}}
    <section class="bg-light !pt-8 !pb-4" id="faq">
        <div class="container !mx-auto">
            <div class="flex justify-between items-start !gap-6 lg:!gap-8 flex-col md:flex-row">
              
              {{-- CONTENT --}}
              <div class="basis-9/12 lg:basis-4/5">
                  <x-card>
                    @foreach ($questions as $group => $items)
                      @php
                        $title = match($group) {
                          'general' => 'General Questions:',
                          'customer' => 'For Customers:',
                          'creator' => 'For Creators:',
                          default => '',
                        }
                      @endphp
                      <div class="!mb-8 last:!mb-0">
                        <x-title class="md:!text-2xl !mb-6">{{ $title }}</x-title>
                        <x-accordion parent="#faq" :items="$items"></x-accordion>
                      </div>
                    @endforeach
                  </x-card>
              </div>

              {{-- LAST NEWS --}}
              <div class="basis-3/12 lg:basis-1/5">
                <x-title class="!font-normal !mb-4 sm:!mb-8">Travel News</x-title>
                <x-last-news></x-last-news>
              </div>

            </div>
        </div>
    </section>

    {{-- CONTACT US --}}
    <section class="bg-light !pt-4 !pb-8" id="contact-us">
      <div class="container">
        @livewire('forms.contact-us')
      </div>
    </section>
@endsection