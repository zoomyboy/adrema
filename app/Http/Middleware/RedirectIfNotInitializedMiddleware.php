<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use App\Setting\NamiSettings;
use Closure;

class RedirectIfNotInitializedMiddleware
{
    public array $dontRedirect = ['initialize.index', 'initialize.store'];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->initialized() && request()->routeIs(['initialize.form'])) {
            return redirect()->to(RouteServiceProvider::HOME);
        }

        if (!$this->shouldRedirect()) {
            return $next($request);
        }

        if (!$this->initialized()) {
            return redirect()->route('initialize.index');
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
