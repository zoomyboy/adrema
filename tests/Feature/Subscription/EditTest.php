<?php

namespace Tests\Feature\Subscription;

use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class EditTest extends TestCase
{
    use DatabaseTransactions;

    public function testItReturnsChildren(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $subscription = Subscription::factory()->name('hi')->forFee()->children([
            new Child('a', 1400),
            new Child('b', 1500),
        ])->create();

        $response = $this->get("/subscription/{$subscription->id}/edit");

        $this->assertInertiaHas([
            'children' => [
                ['name' => 'a', 'amount' => 1400],
                ['name' => 'b', 'amount' => 1500],
            ],
            'name' => 'hi',
            'id' => $subscription->id,
        ], $response, 'data');
    }
}
