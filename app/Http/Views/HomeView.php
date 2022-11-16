<?php

namespace App\Http\Views;

use App\Home\Queries\GroupQuery;
use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Http\Request;

class HomeView
{
    public function index(Request $request): array
    {
        /** @var object{a: string} */
        $amount = Payment::whereNeedsPayment()->selectRaw('sum(subscriptions.amount) AS a')->join('subscriptions', 'subscriptions.id', 'payments.subscription_id')->first();
        $members = Member::whereHasPendingPayment()->count();

        return [
            'data' => [
                'payments' => [
                    'users' => $members,
                    'all_users' => Member::count(),
                    'amount' => number_format($amount->a / 100, 2, ',', '.').' €',
                ],
                'groups' => app(GroupQuery::class)->execute()->getResult(),
                'ending_tries' => MemberTriesResource::collection(Member::endingTries()->get()),
            ],
        ];
    }
}
