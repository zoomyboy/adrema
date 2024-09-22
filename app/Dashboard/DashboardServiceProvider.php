<?php

namespace App\Dashboard;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use App\Dashboard\Actions\IndexAction as DashboardIndexAction;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(DashboardFactory::class, fn () => new DashboardFactory());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        app(Router::class)->middleware(['web', 'auth:web'])->group(function ($router) {
            $router->get('/', DashboardIndexAction::class)->name('home');
        });
    }
}
