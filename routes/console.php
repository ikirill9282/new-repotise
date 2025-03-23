<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\Admin\Page;
use App\Models\Admin\PageSection;
use App\Models\Admin\Section;
use App\Models\Admin\SectionVariables;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Carbon;
use App\Models\Article;
use App\Models\Comment;

Artisan::command('tt', function() {
  $comment = Comment::withCount('likes')->where('id', 3)->first();
  dd($comment->toArray());
});