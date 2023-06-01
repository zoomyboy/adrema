<?php

namespace App\Providers;

use App\Mailgateway\Types\LocalType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        JsonResource::withoutWrapping();
        Telescope::ignoreMigrations();

        \Inertia::share('search', request()->query('search', ''));

        RedirectResponse::macro('success', function ($flash) {
            session()->flash('flash', ['success' => $flash]);

            return $this;
        });

        app()->bind('mail-gateways', fn () => collect([
            LocalType::class,
        ]));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
