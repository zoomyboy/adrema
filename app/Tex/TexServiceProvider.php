<?php

namespace App\Tex;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;

class TexServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        view()->addExtension('tex', 'tex', function () {
            $compiler = new TexCompiler(app('files'), config('view.compiled'));

            return new CompilerEngine($compiler, app('files'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
