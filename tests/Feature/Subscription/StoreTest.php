<?php

namespace Tests\Feature\Subscription;

use App\Fee;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    public function testItStoresASubscription(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $fee = Fee::factory()->create();

        $response = $this->from('/subscription')->post('/subscription', [
            'amount' => 2500,
            'fee_id' => $fee->id,
            'name' => 'Lorem',
        ]);

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

        $response = $this->post('/subscription', [
            'amount' => '',
            'fee_id' => 99,
            'name' => '',
        ]);

        $this->assertErrors([
            'amount' => 'Interner Beitrag ist erforderlich.',
            'fee_id' => 'Nami-Beitrag ist nicht vorhanden.',
            'name' => 'Name ist erforderlich.',
        ], $response);
    }
}
