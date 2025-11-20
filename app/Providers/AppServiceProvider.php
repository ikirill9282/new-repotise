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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Report;
use App\Models\Integration;
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
      
      // Configure Stripe from Integration model if available
      $this->configureStripeFromIntegration();

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
    
    /**
     * Configure Stripe keys from Integration model or fallback to env
     */
    protected function configureStripeFromIntegration(): void
    {
      try {
        // Проверяем доступность подключения к базе данных
        if (!$this->isDatabaseAvailable()) {
          // Если база данных недоступна, используем значения из .env
          return;
        }
        
        $integration = Integration::where('name', 'stripe')
          ->where('status', Integration::STATUS_ACTIVE)
          ->first();
        
        if ($integration) {
          $apiKey = $integration->getConfig('api_key');
          $secretKey = $integration->getConfig('secret_key');
          $webhookSecret = $integration->getConfig('webhook_secret');
          
          if ($apiKey) {
            Config::set('cashier.key', $apiKey);
          }
          
          if ($secretKey) {
            Config::set('cashier.secret', $secretKey);
            // Also set for Stripe SDK directly
            \Stripe\Stripe::setApiKey($secretKey);
          }
          
          if ($webhookSecret) {
            Config::set('cashier.webhook.secret', $webhookSecret);
          }
        }
      } catch (\PDOException $e) {
        // Ошибка подключения к базе данных - используем .env значения
        // Не логируем, так как это нормально при миграциях и консольных командах
      } catch (\Exception $e) {
        // Другие ошибки - логируем только в debug режиме
        if (config('app.debug')) {
          Log::debug('Could not load Stripe config from Integration: ' . $e->getMessage());
        }
      }
    }
    
    /**
     * Check if database connection is available
     */
    protected function isDatabaseAvailable(): bool
    {
      try {
        DB::connection()->getPdo();
        return true;
      } catch (\Exception $e) {
        return false;
      }
    }
}
