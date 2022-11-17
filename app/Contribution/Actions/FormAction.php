<?php

namespace App\Contribution\Actions;

use App\Contribution\ContributionFactory;
use App\Country;
use App\Member\Member;
use App\Member\MemberResource;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class FormAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function handle(): array
    {
        return [
            'allMembers' => MemberResource::collection(Member::slangOrdered()->get()),
            'countries' => Country::pluck('name', 'id'),
            'defaultCountry' => Country::firstWhere('name', 'Deutschland')->id,
            'compilers' => app(ContributionFactory::class)->compilerSelect(),
        ];
    }

    public function asController(): Response
    {
        session()->put('menu', 'contribution');
        session()->put('title', 'Zuschüsse');

        return Inertia::render('contribution/VIndex', $this->handle());
    }
}
