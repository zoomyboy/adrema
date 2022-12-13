<?php

namespace Tests\Feature\Subscription;

use App\Fee;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
            SubscriptionRequestFactory::new()->amount(2500)->fee($fee)->name('lorem')->create()
        );

        $response->assertRedirect('/subscription');
        $this->assertDatabaseHas('subscriptions', [
            'amount' => 2500,
            'fee_id' => $fee->id,
            'name' => 'Lorem',
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
            'amount' => 'Interner Beitrag ist erforderlich.',
            'fee_id' => 'Nami-Beitrag ist nicht vorhanden.',
            'name' => 'Name ist erforderlich.',
        ], $response);
    }
}
