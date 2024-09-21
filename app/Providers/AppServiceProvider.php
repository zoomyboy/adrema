<?php

namespace App\Providers;

use App\Form\Models\Form;
use App\Mailgateway\Types\LocalType;
use App\Mailgateway\Types\MailmanType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Blade;
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

        \Inertia::share('search', request()->query('search', ''));

        RedirectResponse::macro('success', function ($flash) {
            session()->flash('flash', ['success' => $flash]);

            return $this;
        });

        app()->bind('mail-gateways', fn () => collect([
            LocalType::class,
            MailmanType::class,
        ]));

        app()->extend('media-library-helpers', fn ($p) => $p->put('form', Form::class));

        Blade::componentNamespace('App\\View\\Mail', 'mail-view');
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
