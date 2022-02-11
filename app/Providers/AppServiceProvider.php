<?php

namespace App\Providers;

use App\Setting\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Telescope;
use Zoomyboy\LaravelNami\Authentication\NamiGuard;

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

        RedirectResponse::macro('success', function($flash) {
            session()->flash('flash', ['success' => $flash]);

            return $this;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        NamiGuard::beforeLogin(function(array $credentials) {
            return in_array($credentials['mglnr'], app(GeneralSettings::class)->allowed_nami_accounts)
                ? null
                : false;
        });
    }
}
