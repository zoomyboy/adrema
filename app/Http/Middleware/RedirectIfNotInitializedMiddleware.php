<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use App\Setting\NamiSettings;
use Closure;

class RedirectIfNotInitializedMiddleware
{
    /**
     * @var array<int, string>
     */
    public array $dontRedirect = ['initialize.form', 'initialize.store', 'nami.login-check', 'nami.search'];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (1 === preg_match('/\/telescope/', $request->url())) {
            return $next($request);
        }

        if (1 === preg_match('/\/horizon/', $request->url())) {
            return $next($request);
        }

        if ($this->initialized() && request()->routeIs(['initialize.form'])) {
            return redirect()->to(RouteServiceProvider::HOME);
        }

        if (!$this->shouldRedirect()) {
            return $next($request);
        }

        if (!$this->initialized()) {
            return redirect()->route('initialize.form');
        }

        return $next($request);
    }

    public function shouldRedirect(): bool
    {
        return !request()->routeIs($this->dontRedirect) && auth()->check();
    }

    public function initialized(): bool
    {
        return 0 !== app(NamiSettings::class)->default_group_id;
    }
}
