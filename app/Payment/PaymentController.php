<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Http\Views\MemberView;
use App\Member\Member;
use App\Member\MemberResource;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request, Member $member) {
        session()->put('menu', 'member');
        session()->put('title', "Zahlungen für Mitglied {$member->fullname}");

        $payload = app(MemberView::class)->index($request);
        $payload['single'] = app(MemberView::class)->paymentIndex($member);

        return \Inertia::render('member/Index', $payload);
    }

    public function store(Request $request, Member $member) {
        $member->payments()->create($request->validate([
            'nr' => 'required|numeric',
            'subscription_id' => 'required|exists:subscriptions,id',
            'status_id' => 'required|exists:statuses,id',
        ]));

        return redirect()->route('member.payment.index', ['member' => $member]);
    }

    public function edit(Member $member, Request $request, Payment $payment) {
        session()->put('menu', 'member');
        session()->put('title', "Zahlungen für Mitglied {$member->fullname}");

        $payload = app(MemberView::class)->index($request);
        $payload['single'] = app(MemberView::class)->paymentEdit($member, $payment);

        return \Inertia::render('member/Index', $payload);
    }

    public function update(Request $request, Member $member, Payment $payment) {
        $payment->update($request->validate([
            'nr' => 'required|numeric',
            'subscription_id' => 'required|exists:subscriptions,id',
            'status_id' => 'required|exists:statuses,id',
        ]));

        return redirect()->back();
    }

    public function destroy(Request $request, Member $member, Payment $payment) {
        $payment->delete();

        return redirect()->back();
    }
}
