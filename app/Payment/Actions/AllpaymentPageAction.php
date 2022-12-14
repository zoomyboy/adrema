<?php

namespace App\Payment\Actions;

use Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class AllpaymentPageAction
{
    use AsAction;

    /**
     * @return array<string, string>
     */
    public function handle(): array
    {
        return [];
    }

    public function asController(): Response
    {
        session()->put('menu', 'member');
        session()->put('title', 'Rechnungen erstellen');

        return Inertia::render('allpayment/VForm', $this->handle());
    }
}
