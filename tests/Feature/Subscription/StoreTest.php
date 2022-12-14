<?php

namespace Tests\Feature\Subscription;

use App\Fee;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\RequestFactories\SubscriptionRequestFactory;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    public function testItStoresASubscription(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $fee = Fee::factory()->create();

        $response = $this->from('/subscription')->post(
            '/subscription',
            SubscriptionRequestFactory::new()->fee($fee)->name('lorem')->children([
                new Child('ch', 2500),
            ])->create(['split' => true, 'for_promise' => true])
        );

        $response->assertRedirect('/subscription');
        $subscription = Subscription::firstWhere('name', 'lorem');
        $this->assertDatabaseHas('subscriptions', [
            'fee_id' => $fee->id,
            'name' => 'lorem',
            'split' => true,
            'for_promise' => true,
        ]);
        $this->assertDatabaseHas('subscription_children', [
            'name' => 'ch',
            'amount' => 2500,
            'parent_id' => $subscription->id,
        ]);
    }

    public function testItValidatesSubscription(): void
    {
        $this->login()->loginNami();
        $fee = Fee::factory()->create();

        $response = $this->post(
            '/subscription',
            SubscriptionRequestFactory::new()->invalid()->create()
        );

        $this->assertErrors([
            'fee_id' => 'Nami-Beitrag ist nicht vorhanden.',
            'name' => 'Name ist erforderlich.',
            'for_promise' => 'Für Versprechen benutzen muss ein Wahrheitswert sein.',
        ], $response);
    }
}
