<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        // Share parent categories with children to all views using the app layout
        View::composer('layouts.app', function ($view) {
            $navCategories = \App\Models\Category::active()
                ->parents()
                ->with(['children' => fn($q) => $q->active()->orderBy('name')])
                ->orderBy('name')
                ->get();
            $view->with('navCategories', $navCategories);
        });
    }
}
