<?php

namespace App\Http\Views;

use App\Member\MemberResource;
use App\Member\Member;
use Illuminate\Http\Request;
use App\Payment\Status;
use App\Payment\Subscription;
use App\Payment\PaymentResource;
use App\Payment\Payment;

class HomeView {
    public function index(Request $request) {
        $amount = Payment::whereNeedsPayment()->selectRaw('sum(subscriptions.amount) AS a')->join('subscriptions', 'subscriptions.id', 'payments.subscription_id')->first()->a;
        $members = Member::whereHasPendingPayment()->count();

        return [
            'data' => [
                'payments' => [
                    'users' => $members,
                    'all_users' => Member::count(),
                    'amount' => number_format($amount / 100, 2, ',', '.').' €'
                ]
            ]
        ];
    }

}
