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
use App\Models\Options;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;

Artisan::command('tt', function() {
  $m = new VerifyMail([
    'name' => 'Demo',
  ]);
  $mail = Mail::to('perepelitso01@gmail.com')->send($m);

  dd($mail);
});