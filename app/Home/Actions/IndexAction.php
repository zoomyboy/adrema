<?php

namespace App\Home\Actions;

use App\Home\DashboardFactory;
use Illuminate\Http\Request;
use Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class IndexAction
{
    use AsAction;

    /**
     * @return array<array-key, mixed>
     */
    public function handle(): array
    {
        return [
            'blocks' => app(DashboardFactory::class)->render(),
        ];
    }

    public function asController(Request $request): Response
    {
        session()->put('menu', 'dashboard');
        session()->put('title', 'Dashboard');

        return Inertia::render('home/VIndex', $this->handle());
    }
}
