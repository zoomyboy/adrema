<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use App\Module\ModuleSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
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
                'modules' => app(ModuleSettings::class)->modules,
            ],
        ];
    }
}
