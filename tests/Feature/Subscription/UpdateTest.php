<?php

namespace Tests\Feature\Subscription;

use App\Fee;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\RequestFactories\SubscriptionRequestFactory;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function testItUpdatesASubscription(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $subscription = Subscription::factory()->name('hi')->for(Fee::factory())->create(['split' => true, 'for_promise' => false]);
        $fee = Fee::factory()->create();

        $response = $this->from("/subscription/{$subscription->id}")->patch(
            "/subscription/{$subscription->id}",
            SubscriptionRequestFactory::new()->amount(2500)->fee($fee)->name('lorem')->create(['split' => false, 'for_promise' => true])
        );

        $response->assertRedirect('/subscription');
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'fee_id' => $fee->id,
            'name' => 'Lorem',
            'split' => false,
            'for_promise' => true,
        ]);
    }

    public function testItUpdatesChildren(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $subscription = Subscription::factory()->name('hi')->for(Fee::factory())->children([
            new Child('a', 1400),
            new Child('b', 1500),
        ])->create();

        $response = $this->from("/subscription/{$subscription->id}")->patch(
            "/subscription/{$subscription->id}",
            SubscriptionRequestFactory::new()->children([
                new Child('c', 1900),
            ])->create()
        );

        $response->assertRedirect('/subscription');
        $this->assertDatabaseHas('subscription_children', [
            'parent_id' => $subscription->id,
            'name' => 'c',
            'amount' => 1900,
        ]);
        $this->assertDatabaseCount('subscription_children', 1);
    }

    public function testItValidatesRequest(): void
    {
        $this->login()->loginNami();
        $subscription = Subscription::factory()->name('hi')->for(Fee::factory())->create();

        $response = $this->from("/subscription/{$subscription->id}")->patch(
            "/subscription/{$subscription->id}",
            SubscriptionRequestFactory::new()->invalid()->create()
        );

        $this->assertErrors([
            'fee_id' => 'Nami-Beitrag ist nicht vorhanden.',
            'name' => 'Name ist erforderlich.',
            'for_promise' => 'Für Versprechen benutzen muss ein Wahrheitswert sein.',
        ], $response);
    }
}