<?php

namespace App\Setting;

use Closure;
use Illuminate\Http\Request;
use Inertia;

class SettingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Inertia::share([
            'setting_menu' => app(SettingFactory::class)->getShare(),
        ]);

        return $next($request);
    }
}
