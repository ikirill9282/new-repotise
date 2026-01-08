@extends('layouts.site')

@section('content')
  @php
    $config = $page->config ?? collect();
    
    // Безопасная фильтрация с проверкой на null
    $filterConfig = function($prefix) use ($config) {
      if ($config->isEmpty()) {
        return collect();
      }
      return $config->filter(function($record) use ($prefix) {
        return isset($record->name) && str_starts_with($record->name, $prefix);
      })->keyBy('name');
    };
    
    $pageVars = $filterConfig('page');
    $mainArticleVars = $filterConfig('main_article');
    $insightsVars = $filterConfig('insights');
    $newsVars = $filterConfig('news');
    $productsVars = $filterConfig('products');
    $authorsVars = $filterConfig('authors');
  @endphp
  
  @include('site.sections.home', ['variables' => $pageVars])
  @include('site.sections.main_article', ['variables' => $mainArticleVars])
  @include('site.sections.insights', ['variables' => $insightsVars])
  @include('site.sections.news', ['variables' => $newsVars])
  @include('site.sections.products', ['variables' => $productsVars])
  @include('site.sections.authors', ['variables' => $authorsVars])
@endsection