<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Http\Views\MemberView;
use App\Member\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request, Member $member): Response
    {
        session()->put('menu', 'member');
        session()->put('title', "Zahlungen für Mitglied {$member->fullname}");

        $payload = app(MemberView::class)->index($request, []);
        $payload['single'] = app(MemberView::class)->paymentIndex($member);

        return \Inertia::render('member/VIndex', $payload);
    }

    public function store(Request $request, Member $member): RedirectResponse
    {
        $member->createPayment($request->validate([
            'nr' => 'required|numeric',
            'subscription_id' => 'required|exists:subscriptions,id',
            'status_id' => 'required|exists:statuses,id',
        ]));

        return redirect()->back();
    }

    public function edit(Member $member, Request $request, Payment $payment): Response
    {
        session()->put('menu', 'member');
        session()->put('title', "Zahlungen für Mitglied {$member->fullname}");

        $payload = app(MemberView::class)->index($request, []);
        $payload['single'] = app(MemberView::class)->paymentEdit($member, $payment);

        return \Inertia::render('member/VIndex', $payload);
    }

    public function update(Request $request, Member $member, Payment $payment): RedirectResponse
    {
        $payment->update($request->validate([
            'nr' => 'required|numeric',
            'subscription_id' => 'required|exists:subscriptions,id',
            'status_id' => 'required|exists:statuses,id',
        ]));

        return redirect()->back();
    }

    public function destroy(Request $request, Member $member, Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->back();
    }
}
