<?php

namespace App\Dashboard\Actions;

use App\Dashboard\DashboardFactory;
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

    public function asController(): Response
    {
        session()->put('menu', 'dashboard');
        session()->put('title', 'Dashboard');

        return Inertia::render('dashboard/VIndex', $this->handle());
    }
}
