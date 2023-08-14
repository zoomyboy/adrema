<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use App\Lib\Events\ClientMessage;
use App\Member\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Member $member): RedirectResponse
    {
        $member->createPayment($request->validate([
            'nr' => 'required',
            'subscription_id' => 'required|exists:subscriptions,id',
            'status_id' => 'required|exists:statuses,id',
        ]));

        ClientMessage::make('Zahlung erstellt.')->shouldReload()->dispatch();

        return redirect()->back();
    }

    public function update(Request $request, Member $member, Payment $payment): RedirectResponse
    {
        $payment->update($request->validate([
            'nr' => 'required',
            'subscription_id' => 'required|exists:subscriptions,id',
            'status_id' => 'required|exists:statuses,id',
        ]));

        ClientMessage::make('Zahlung aktualisiert.')->shouldReload()->dispatch();

        return redirect()->back();
    }

    public function destroy(Request $request, Member $member, Payment $payment): RedirectResponse
    {
        $payment->delete();

        ClientMessage::make('Zahlung gelöscht.')->shouldReload()->dispatch();

        return redirect()->back();
    }
}
