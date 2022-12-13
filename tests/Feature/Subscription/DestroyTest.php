<?php

namespace Tests\Feature\Subscription;

use App\Fee;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDeletesChildrenWithSubscription(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $subscription = Subscription::factory()->name('hi')->for(Fee::factory())->children([
            new Child('a', 1400),
            new Child('b', 1500),
        ])->create();

        $response = $this->from('/subscription')->delete("/subscription/{$subscription->id}");

        $response->assertRedirect('/subscription');
        $this->assertDatabaseCount('subscription_children', 0);
        $this->assertDatabaseCount('subscriptions', 0);
    }
}
