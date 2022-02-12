<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotInitializedMiddleware
{

    public array $dontRedirect = ['initialize.index', 'initialize.store'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$this->shouldRedirect()) {
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
        return \App\Fee::count() > 0;
    }
}
