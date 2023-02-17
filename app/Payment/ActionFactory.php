<?php

namespace App\Payment;

use App\Letter\DocumentFactory;
use Illuminate\Support\Collection;

class ActionFactory
{
    /**
     * @return Collection<int, array{link: array{href: string, label: mixed}, text: mixed}>
     */
    public function allLinks(): Collection
    {
        return app(DocumentFactory::class)->getTypes()->map(function ($repo) {
            return [
                'link' => [
                    'href' => route('sendpayment.pdf', ['type' => $repo]),
                    'label' => $repo::sendAllLabel(),
                ],
                'text' => $repo::getDescription(),
            ];
        });
    }
}
