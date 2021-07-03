<?php

namespace App\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Fee;

class SubscriptionController extends Controller
{
    public function index(Request $request) {
        session()->put('menu', 'subscription');
        session()->put('title', 'Beiträge');

        return \Inertia::render('subscription/Index', [
            'data' => SubscriptionResource::collection(Subscription::get()),
            'toolbar' => [ ['href' => route('subscription.create'), 'label' => 'Beitrag anlegen', 'color' => 'primary', 'icon' => 'plus'] ],
        ]);
    }

    public function create() {
        session()->put('menu', 'subscription');
        session()->put('title', 'Beitrag erstellen');

        return \Inertia::render('subscription/Form', [
            'fees' => Fee::get()->pluck('name', 'id'),
            'mode' => 'create',
            'data' => (object) []
        ]);
    }

    public function store(Request $request) {
        Subscription::create($request->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric',
            'fee_id' => 'required|exists:fees,id',
        ]));

        return redirect()->route('subscription.index');
    }

    public function edit(Subscription $subscription, Request $request) {
        session()->put('menu', 'subscription');
        session()->put('title', "Beitrag {$subscription->name} bearbeiten");

        return \Inertia::render('subscription/Form', [
            'fees' => Fee::get()->pluck('name', 'id'),
            'mode' => 'edit',
            'data' => new SubscriptionResource($subscription),
        ]);
    }

    public function update(Subscription $subscription, Request $request) {
        $subscription->update($request->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric',
            'fee_id' => 'required|exists:fees,id',
        ]));

        return redirect()->route('subscription.index');
    }
}
