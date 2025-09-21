@extends('layouts.site')

@section('content')
  <section class="!py-12" id="create-article">
      <div class="container">
        <h1 class="!font-normal !m-0 !mb-10">Create Article</h1>
        <x-breadcrumbs class="!mb-10" :breadcrumbs="[
          'My Account' => route('profile'),
          'My Articles' => route('profile.articles'),
          'Create Article' => route('profile.articles.create'),
        ]" />

        @livewire('forms.article', [
          'article_id' => $article_id,
        ])
      </div>
  </section>
@endsection
