<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotInitializedMiddleware
{

    public $dontRedirect = ['initialize.index', 'initialize.store'];

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

    public function shouldRedirect() {
        return !in_array(request()->route()->getName(), $this->dontRedirect) && auth()->check();
    }

    public function initialized() {
        return \App\Fee::get()->count() > 0;
    }
}
