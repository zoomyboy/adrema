<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use App\Setting\GeneralSettings;
use Closure;
use Session;

class InertiaShareMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Inertia::share([
            'auth' => ['user' => auth()->check() ? new UserResource(auth()->user()) : null],
            'search' => $request->query('search', ''),
            'flash' => session()->get('flash'),
            'menu' => function () {
                return session()->get('menu');
            },
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'title' => function () {
                return session()->get('title', '');
            },
            'settings' => [
                'modules' => app(GeneralSettings::class)->modules,
            ],
        ]);

        $response = $next($request);

        return $response;
    }
}
