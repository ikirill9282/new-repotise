@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <x-card class="">
      <div class="flex justify-between items-center mb-12">
        <h2 class="font-bold text-2xl">Travel Insights</h2>
        <div class="flex justify-center items-center gap-2">
          <x-btn href="{{ route('profile.articles.create') }}" class="!px-8 !py-1.5 text-nowrap" >Add Article</x-btn>
        </div>
      </div>
      @livewire('profile.tables', [
        'active' => 'articles-published',
        'sortable' => true,
        'tables' => [
          [
            'name' => "articles-published",
            'title' => "Published (". $user->products()->where('status_id', 1)->count() .")",
          ],
          [
            'name' => 'articles-scheduled',
            'title' => "Scheduled (". $user->products()->where('status_id', 2)->count() .")",
          ],
          [
            'name' => 'articles-draft',
            'title' => "Draft (". $user->products()->where('status_id', 3)->count() .")",
          ]
        ]
      ])
    </x-card>
  </x-profile.wrap>
@endsection