<?php

namespace App\Setting;

use App\Letter\LetterSettings;
use App\Mailman\MailmanSettings;
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
        app(SettingFactory::class)->register(LetterSettings::class);
        app(SettingFactory::class)->register(MailmanSettings::class);
    }
}
