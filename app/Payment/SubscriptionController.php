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

        return \Inertia::render('subscription/SubscriptionIndex', [
            'data' => SubscriptionResource::collection(Subscription::get()),
            'toolbar' => [['href' => route('subscription.create'), 'label' => 'Beitrag anlegen', 'color' => 'primary', 'icon' => 'plus']],
        ]);
    }

    public function create(): Response
    {
        session()->put('menu', 'subscription');
        session()->put('title', 'Beitrag erstellen');

        return \Inertia::render('subscription/SubscriptionForm', [
            'fees' => Fee::pluck('name', 'id'),
            'mode' => 'create',
            'data' => [
                'children' => [],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $subscriptionParams = $request->validate([
            'name' => 'required|max:255',
            'split' => 'present|boolean',
            'fee_id' => 'required|exists:fees,id',
            'for_promise' => 'present|boolean',
        ], [], [
            'fee_id' => 'Nami-Beitrag',
            'for_promise' => 'Für Versprechen benutzen',
        ]);

        $children = $request->validate([
            'children' => 'present|array',
            'children.*.amount' => 'required|numeric',
            'children.*.name' => 'required|max:255',
        ]);

        $subscription = Subscription::create($subscriptionParams);
        $subscription->children()->createMany($children['children']);

        return redirect()->route('subscription.index');
    }

    public function edit(Subscription $subscription, Request $request): Response
    {
        session()->put('menu', 'subscription');
        session()->put('title', "Beitrag {$subscription->name} bearbeiten");

        return \Inertia::render('subscription/SubscriptionForm', [
            'fees' => Fee::pluck('name', 'id'),
            'mode' => 'edit',
            'data' => new SubscriptionResource($subscription),
        ]);
    }

    public function update(Subscription $subscription, Request $request): RedirectResponse
    {
        $subscriptionParams = $request->validate([
            'name' => 'required|max:255',
            'split' => 'present|boolean',
            'fee_id' => 'required|exists:fees,id',
            'for_promise' => 'present|boolean',
        ], [], [
            'fee_id' => 'Nami-Beitrag',
            'for_promise' => 'Für Versprechen benutzen',
        ]);
        $subscription->update($subscriptionParams);
        $children = $request->validate([
            'children' => 'present|array',
            'children.*.amount' => 'required|numeric',
            'children.*.name' => 'required|max:255',
        ]);
        $subscription->children()->delete();
        $subscription->children()->createMany($children['children']);

        return redirect()->route('subscription.index');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('subscription.index');
    }
}
