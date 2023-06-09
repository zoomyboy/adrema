<?php

namespace App\Payment;

use App\Dashboard\Blocks\Block;
use App\Member\Member;

class MemberPaymentBlock extends Block
{
    /**
     * @return array<string, string|int>
     */
    public function data(): array
    {
        $amount = Payment::whereNeedsPayment()
            ->selectRaw('sum(subscription_children.amount) AS nr')
            ->join('subscriptions', 'subscriptions.id', 'payments.subscription_id')
            ->join('subscription_children', 'subscriptions.id', 'subscription_children.parent_id')
            ->first();
        $members = Member::whereHasPendingPayment()->count();

        return [
            'members' => $members,
            'total_members' => Member::count(),
            'amount' => number_format((int) $amount->nr / 100, 2, ',', '.').' €',
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
