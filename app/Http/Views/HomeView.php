<?php

namespace App\Http\Views;

use App\Member\Member;
use App\Member\MemberResource;
use App\Payment\Payment;
use App\Payment\PaymentResource;
use App\Payment\Status;
use App\Payment\Subscription;
use Illuminate\Http\Request;

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
                ],
                'groups' => Member::select('subactivities.slug', 'subactivities.name')->selectRaw('COUNT(members.id) AS count')->join('memberships', 'memberships.member_id', 'members.id')
                    ->join('activities', 'memberships.activity_id', 'activities.id')
                    ->join('subactivities', 'memberships.subactivity_id', 'subactivities.id')
                    ->where('subactivities.is_age_group', true)
                    ->where('activities.is_member', true)
                    ->groupBy('subactivities.name', 'subactivities.slug')
                    ->orderBy('subactivities.id')
                    ->get()
            ]
        ];
    }

}
