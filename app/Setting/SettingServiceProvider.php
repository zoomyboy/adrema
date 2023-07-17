<?php

namespace App\Setting;

use App\Invoice\InvoiceSettings;
use App\Mailgateway\MailgatewaySettings;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(SettingFactory::class, fn () => new SettingFactory());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        app(SettingFactory::class)->register(InvoiceSettings::class);
        app(SettingFactory::class)->register(MailgatewaySettings::class);
    }
}
