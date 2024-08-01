<?php

namespace App\Setting;

use App\Fileshare\FileshareSettings;
use App\Form\FormSettings;
use App\Invoice\InvoiceSettings;
use App\Mailgateway\MailgatewaySettings;
use App\Module\ModuleSettings;
use App\Prevention\PreventionSettings;
use App\Setting\Actions\StoreAction;
use App\Setting\Actions\ViewAction;
use Illuminate\Routing\Router;
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
        app(Router::class)->bind('settingGroup', fn ($param) => app(SettingFactory::class)->resolveGroupName($param));
        app(Router::class)->middleware(['web', 'auth:web'])->name('setting.view')->get('/setting/{settingGroup}', ViewAction::class);
        app(Router::class)->middleware(['web', 'auth:web'])->name('setting.data')->get('/setting/{settingGroup}/data', ViewAction::class);
        app(Router::class)->middleware(['web', 'auth:web'])->name('setting.store')->post('/setting/{settingGroup}', StoreAction::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        app(SettingFactory::class)->register(ModuleSettings::class);
        app(SettingFactory::class)->register(InvoiceSettings::class);
        app(SettingFactory::class)->register(MailgatewaySettings::class);
        app(SettingFactory::class)->register(NamiSettings::class);
        app(SettingFactory::class)->register(FormSettings::class);
        app(SettingFactory::class)->register(FileshareSettings::class);
        app(SettingFactory::class)->register(PreventionSettings::class);
    }
}
