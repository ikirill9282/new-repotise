<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Opcodes\LogViewer\Facades\LogViewer;
use Laravel\Cashier\Cashier;
use App\Models\Subscriptions;
use Illuminate\Support\Facades\Blade;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Report;
use App\Observers\ArticleObserver;
use App\Observers\CommentObserver;
use App\Observers\ReportObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
      Model::unguard();

      Cashier::useSubscriptionModel(Subscriptions::class);

      // Register observers
      Article::observe(ArticleObserver::class);
      Comment::observe(CommentObserver::class);
      Report::observe(ReportObserver::class);

      // LogViewer::auth(function ($request) {
      //   return $request->user() && ($request->user()->hasRole('admin') || $request->user()->hasRole('super-admin'));
      // });
    
      // Register Blade component namespace for analytics components
      Blade::anonymousComponentNamespace('filament.pages.analytics.components', 'filament-pages-analytics');

      Filament::serving(function () {
        Filament::registerNavigationGroups([
            NavigationGroup::make('analytics')
                ->label('Analytics')
                ->icon('heroicon-o-chart-bar')
                ->collapsed(),
            NavigationGroup::make('users')
                ->label('Users')
                ->icon('heroicon-o-user-group')
                ->collapsed(),
            NavigationGroup::make('content')
                ->label('Content')
                ->icon('heroicon-o-document-text')
                ->collapsed(),
            NavigationGroup::make('products')
                ->label('Products')
                ->icon('heroicon-o-shopping-bag')
                ->collapsed(),
            NavigationGroup::make('financials')
                ->label('Financials')
                ->icon('heroicon-o-banknotes')
                ->collapsed(),
            NavigationGroup::make('community')
                ->label('Community')
                ->icon('heroicon-o-users')
                ->collapsed(),
            NavigationGroup::make('marketing')
                ->label('Marketing')
                ->icon('heroicon-o-megaphone')
                ->collapsed(),
            NavigationGroup::make('settings')
                ->label('Settings')
                ->icon('heroicon-o-cog-6-tooth')
                ->collapsed(),
            NavigationGroup::make('other')
                ->label('Other')
                ->icon('heroicon-o-ellipsis-horizontal-circle')
                ->collapsed(),
        ]);
    });
    }
}
