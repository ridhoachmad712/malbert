<?php

namespace App\Providers;

use App\Models\NavigationItem;
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
            $view->with('appearance', SchoolSetting::appearance());
        });
        \Illuminate\Support\Facades\View::composer('layouts.app', function (View $view) {
            if (! request()->is('kelola*')) {
                $view->with('navigationItems', NavigationItem::with('sitePage')->where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get()->filter(fn (NavigationItem $item) => $item->url() !== null));
            }
        });
        Paginator::defaultView('components.pagination');
    }
}
