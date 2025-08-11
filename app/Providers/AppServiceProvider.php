<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Opcodes\LogViewer\Facades\LogViewer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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

      LogViewer::auth(function ($request) {
        return $request->user() && ($request->user()->hasRole('admin') || $request->user()->hasRole('super-admin'));
      });
    

      Filament::serving(function () {
        Filament::registerNavigationGroups([
            NavigationGroup::make('products')
                 ->label('Products')
                 ,
            NavigationGroup::make('articles')
                ->label('Articles')
                ->collapsed()
                ,
            NavigationGroup::make('content')
                ->label('Content')
                ,
            NavigationGroup::make('users')
                ->label('Users')
                ->collapsed()
                ,
        ]);
    });
    }
}
