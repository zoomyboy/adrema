<?php

namespace Tests\Feature\Payment;

use App\Member\Member;
use App\Payment\Status;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AllpaymentTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login()->loginNami();
    }

    public function testItDoesntCreatePaymentsWithoutSubscription(): void
    {
        $member = Member::factory()->defaults()->emailBillKind()->create();
        $member->update(['subscription_id' => null]);

        $response = $this->from('/allpayment/create')->post('allpayment', [
            'year' => now()->addYear()->year,
        ]);

        $response->assertRedirect('/allpayment/create');
        $this->assertEmpty($member->payments()->get());
    }

    public function testItDoesntCreatePaymentWithoutBillKind(): void
    {
        $member = Member::factory()->defaults()->create();

        $response = $this->from('/allpayment/create')->post('allpayment', [
            'year' => now()->addYear()->year,
        ]);

        $response->assertRedirect('/allpayment/create');
        $this->assertEmpty($member->payments()->get());
    }

    public function testItCreatesPayments(): void
    {
        $member = Member::factory()->defaults()->emailBillKind()->create();

        $response = $this->from('/allpayment/create')->post('allpayment', [
            'year' => now()->addYear()->year,
        ]);

        $response->assertRedirect('/allpayment/create');
        $this->assertDatabaseHas('payments', [
            'member_id' => $member->id,
            'nr' => now()->addYear()->year,
            'subscription_id' => $member->subscription->id,
            'status_id' => Status::first()->id,
        ]);
    }
}
