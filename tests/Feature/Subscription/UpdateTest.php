<?php

namespace Tests\Feature\Subscription;

use App\Fee;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\SubscriptionRequestFactory;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function testItUpdatesASubscription(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $subscription = Subscription::factory()->amount(670)->name('hi')->for(Fee::factory())->create();
        $fee = Fee::factory()->create();

        $response = $this->from("/subscription/{$subscription->id}")->patch(
            "/subscription/{$subscription->id}",
            SubscriptionRequestFactory::new()->amount(2500)->fee($fee)->name('lorem')->create()
        );

        $response->assertRedirect('/subscription');
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'amount' => 2500,
            'fee_id' => $fee->id,
            'name' => 'Lorem',
        ]);
    }

    public function testItValidatesRequest(): void
    {
        $this->login()->loginNami();
        $subscription = Subscription::factory()->amount(670)->name('hi')->for(Fee::factory())->create();
        $fee = Fee::factory()->create();

        $response = $this->from("/subscription/{$subscription->id}")->patch(
            "/subscription/{$subscription->id}",
            SubscriptionRequestFactory::new()->invalid()->create()
        );

        $this->assertErrors([
            'amount' => 'Interner Beitrag ist erforderlich.',
            'fee_id' => 'Nami-Beitrag ist nicht vorhanden.',
            'name' => 'Name ist erforderlich.',
        ], $response);
    }
}
