<?php

namespace App\Invoice;

use App\Dashboard\Blocks\Block;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;

class MemberPaymentBlock extends Block
{
    /**
     * @return array<string, string|int>
     */
    public function data(): array
    {
        $amount = InvoicePosition::whereHas('invoice', fn ($query) => $query->whereNeedsPayment())
            ->selectRaw('sum(price) AS price')
            ->first();
        $members = Member::whereHasPendingPayment()->count();

        return [
            'members' => $members,
            'total_members' => Member::count(),
            'amount' => number_format((int) $amount->price / 100, 2, ',', '.') . ' €',
        ];
    }

    public function component(): string
    {
        return 'member-payment';
    }

    public function title(): string
    {
        return 'Ausstehende Mitgliedsbeiträge';
    }
}
