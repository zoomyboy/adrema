<?php

namespace App\Http\Middleware;

use Session;
use Closure;
use App\Http\Resources\UserResource;

class InertiaShareMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Inertia::share([
            'auth' => ['user' => auth()->check() ? new UserResource(auth()->user()) : null]
        ]);

        \Inertia::share([
            'menu' => session()->get('menu'),
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'title' => function() {
                return session()->get('title', '');
            }
        ]);

        $response = $next($request);

        return $response;
    }
}
