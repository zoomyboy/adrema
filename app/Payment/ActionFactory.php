<?php

namespace App\Payment;

use App\Member\Member;
use App\Pdf\PdfRepositoryFactory;
use Illuminate\Support\Collection;

class ActionFactory
{

    public function forMember(Member $member): Collection
    {
        return app(PdfRepositoryFactory::class)->getTypes()->map(function(string $repo) use ($member) {
            $repo = app($repo);

            return [
                'href' => route('member.singlepdf', ['member' => $member, 'type' => get_class($repo)]),
                'label' => $repo->linkLabel(),
                'disabled' => !$repo->createable($member),
            ];
        });
    }

    public function allLinks(): Collection
    {
        return app(PdfRepositoryFactory::class)->getTypes()->map(function(string $repo) {
            $repo = app($repo);

            return [
                'link' => [
                    'href' => route('sendpayment.pdf', ['type' => get_class($repo)]),
                    'label' => $repo->allLabel(),
                ],
                'text' => $repo->getDescription(),
            ];
        });
    }

}
