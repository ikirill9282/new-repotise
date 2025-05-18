<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;

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

      Filament::serving(function () {
        Filament::registerNavigationGroups([
            NavigationGroup::make('products')
                 ->label('Products')
                 ,
            NavigationGroup::make('content')
                ->label('Content')
                ,
            NavigationGroup::make('articles')
                ->label('Articles')
                ->collapsed(),
            NavigationGroup::make('users')
                ->label('Users')
                ->collapsed(),
        ]);
    });
    }
}
