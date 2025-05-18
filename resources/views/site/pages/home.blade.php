@extends('layouts.site')

{{-- @dd($page->config->keyBy('name')) --}}

@section('content')
  @include('site.sections.home', ['variables' => $page->config->where(fn($record) => str_starts_with( $record->name, 'page'))->keyBy('name') ?? []])
  @include('site.sections.main_article', ['variables' => $page->config->where(fn($record) => str_starts_with( $record->name, 'main_article'))->keyBy('name') ?? []])
  @include('site.sections.insights', ['variables' => $page->config->where(fn($record) => str_starts_with( $record->name, 'insights'))->keyBy('name') ?? []])
  @include('site.sections.news', ['variables' => $page->config->where(fn($record) => str_starts_with( $record->name, 'news'))->keyBy('name') ?? []])
  @include('site.sections.products', ['variables' => $page->config->where(fn($record) => str_starts_with( $record->name, 'products'))->keyBy('name') ?? []])
  @include('site.sections.authors', ['variables' => $page->config->where(fn($record) => str_starts_with( $record->name, 'authors'))->keyBy('name') ?? []])
@endsection