<?php

namespace App\Payment;

use App\Letter\DocumentFactory;
use App\Letter\Letter;
use Illuminate\Support\Collection;

class ActionFactory
{
    public function allLinks(): Collection
    {
        return app(DocumentFactory::class)->getTypes()->map(function (Letter $repo) {
            return [
                'link' => [
                    'href' => route('sendpayment.pdf', ['type' => get_class($repo)]),
                    'label' => $repo->sendAllLabel(),
                ],
                'text' => $repo->getDescription(),
            ];
        });
    }
}
