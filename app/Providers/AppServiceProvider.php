<?php

namespace App\Providers;

use App\Models\SchoolSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

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
        \Illuminate\Support\Facades\View::composer('*', function (View $view) {
            $view->with('school', SchoolSetting::values());
        });
        Paginator::defaultView('components.pagination');
    }
}
