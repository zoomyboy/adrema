<?php

namespace App\Payment;

use App\Fee;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        session()->put('menu', 'subscription');
        session()->put('title', 'Beiträge');

        return \Inertia::render('subscription/Index', [
            'data' => SubscriptionResource::collection(Subscription::get()),
            'toolbar' => [ ['href' => route('subscription.create'), 'label' => 'Beitrag anlegen', 'color' => 'primary', 'icon' => 'plus'] ],
        ]);
    }

    public function create(): Response
    {
        session()->put('menu', 'subscription');
        session()->put('title', 'Beitrag erstellen');

        return \Inertia::render('subscription/Form', [
            'fees' => Fee::pluck('name', 'id'),
            'mode' => 'create',
            'data' => (object) []
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Subscription::create($request->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric',
            'fee_id' => 'required|exists:fees,id',
        ]));

        return redirect()->route('subscription.index');
    }

    public function edit(Subscription $subscription, Request $request): Response
    {
        session()->put('menu', 'subscription');
        session()->put('title', "Beitrag {$subscription->name} bearbeiten");

        return \Inertia::render('subscription/Form', [
            'fees' => Fee::pluck('name', 'id'),
            'mode' => 'edit',
            'data' => new SubscriptionResource($subscription),
        ]);
    }

    public function update(Subscription $subscription, Request $request): RedirectResponse
    {
        $subscription->update($request->validate([
            'name' => 'required|max:255',
            'amount' => 'required|numeric',
            'fee_id' => 'required|exists:fees,id',
        ]));

        return redirect()->route('subscription.index');
    }
}
