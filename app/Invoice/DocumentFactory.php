<?php

namespace App\Invoice;

use App\Member\Member;
use Illuminate\Support\Collection;

class DocumentFactory
{
    /**
     * @var array<int, class-string<Invoice>>
     */
    private array $types = [
        BillDocument::class,
        RememberDocument::class,
    ];

    /**
     * @return Collection<int, class-string<Invoice>>
     */
    public function getTypes(): Collection
    {
        return collect($this->types);
    }

    /**
     * @param Collection<(int|string), Member> $members
     */
    public function afterSingle(Invoice $invoice, Collection $members): void
    {
        foreach ($members as $member) {
            foreach ($member->payments as $payment) {
                $invoice->afterSingle($payment);
            }
        }
    }
}
