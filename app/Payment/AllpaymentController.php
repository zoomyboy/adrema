<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Member\Member;
use App\Payment\Status;

class AllpaymentController extends Controller
{
    public function create() {
        session()->put('menu', 'member');
        session()->put('title', 'Rechnungen erstellen');

        return \Inertia::render('allpayment/Form', [
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'year' => 'required|numeric'
        ]);

        foreach (Member::payable()->whereNoPayment($request->year)->get() as $member) {
            $member->payments()->create([
                'nr' => $request->year,
                'subscription_id' => $member->subscription_id,
                'status_id' => Status::default(),
            ]);
        }

        return redirect()->back();
    }
}