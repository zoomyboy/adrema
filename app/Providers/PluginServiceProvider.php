<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(base_path('plugins/*/ServiceProvider.php')) as $file) {
            $cls = (string) str($file)
                ->replace(base_path('plugins/'), '')
                ->replaceMatches('/\.php$/', '')
                ->replace('/', '\\');
            $this->app->register('Plugins\\'.$cls);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
