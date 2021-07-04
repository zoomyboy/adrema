<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Member\Member;
use App\Member\MemberResource;
use App\Http\Views\MemberView;

class PaymentController extends Controller
{
    public function index(Request $request, Member $member) {
        session()->put('menu', 'member');
        session()->put('title', "Zahlungen für Mitglied {$member->fullname}");

        $payload = app(MemberView::class)->index($request);
        $payload['single'] = app(MemberView::class)->paymentIndex($member);

        return \Inertia::render('member/Index', $payload);
    }

    public function create(Member $member, Request $request) {
        session()->put('menu', 'member');
        session()->put('title', "Zahlungen für Mitglied {$member->fullname}");

        $payload = app(MemberView::class)->index($request);
        $payload['single'] = app(MemberView::class)->paymentCreate($member);

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

        return redirect()->route('member.payment.index', ['member' => $member]);
    }

    public function destroy(Request $request, Member $member, Payment $payment) {
        $payment->delete();

        return redirect()->route('member.payment.index', ['member' => $member]);
    }
}
