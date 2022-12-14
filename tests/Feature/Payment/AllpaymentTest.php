<?php

namespace Tests\Feature\Payment;

use App\Member\Member;
use App\Member\Membership;
use App\Payment\Payment;
use App\Payment\Status;
use App\Payment\Subscription;
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
            'for_promise' => false,
        ]);

        $response->assertRedirect('/allpayment/create');
        $this->assertDatabaseHas('payments', [
            'member_id' => $member->id,
            'nr' => now()->addYear()->year,
            'subscription_id' => $member->subscription->id,
            'status_id' => Status::first()->id,
        ]);
    }

    public function testItCreatesPromisePayments(): void
    {
        $member = Member::factory()
            ->defaults()
            ->emailBillKind()
            ->has(Membership::factory()->in('€ Mitglied', 123, 'Rover', 124)->promise(now()->subYear()->startOfYear()))
            ->create();

        $subscription = Subscription::factory()->forPromise()->create();

        $this->from('/allpayment/create')->post('allpayment', [
            'year' => now()->year,
            'for_promise' => true,
        ]);

        $this->assertDatabaseHas('payments', [
            'member_id' => $member->id,
            'nr' => 'Rover '.now()->subYear()->year,
            'subscription_id' => $subscription->id,
            'status_id' => Status::first()->id,
        ]);
    }

    public function testItDoesntCreatePromisePaymentsWhenPromiseIsOver(): void
    {
        $member = Member::factory()
            ->defaults()
            ->emailBillKind()
            ->has(Membership::factory()->in('€ Mitglied', 123, 'Rover', 124)->promise(now()->subYears(2)->startOfYear()))
            ->create();

        $subscription = Subscription::factory()->forPromise()->create();

        $this->from('/allpayment/create')->post('allpayment', [
            'year' => now()->year,
            'for_promise' => true,
        ]);

        $this->assertDatabaseMissing('payments', [
            'subscription_id' => $subscription->id,
        ]);
    }

    public function testItDoesntCreatePromisePaymentsWhenUserAlreadyHasPayment(): void
    {
        $subscription = Subscription::factory()->forPromise()->create();

        $member = Member::factory()
            ->defaults()
            ->emailBillKind()
            ->has(Membership::factory()->in('€ Mitglied', 123, 'Rover', 124)->promise(now()->subYear()->startOfYear()))
            ->has(Payment::factory()->notPaid()->nr('Rover '.now()->subYear()->year)->for($subscription))
            ->create();

        $this->from('/allpayment/create')->post('allpayment', [
            'year' => now()->year,
            'for_promise' => true,
        ]);

        $this->assertCount(2, $member->payments);
    }

    public function testItDoesntCreatePromisePaymentsWhenNoSubscriptionFound(): void
    {
        $member = Member::factory()
            ->defaults()
            ->emailBillKind()
            ->has(Membership::factory()->in('€ Mitglied', 123, 'Rover', 124)->promise(now()->subYear()->startOfYear()))
            ->has(Payment::factory()->notPaid()->nr('Rover '.now()->subYear()->year))
            ->create();

        $this->from('/allpayment/create')->post('allpayment', [
            'year' => now()->year,
            'for_promise' => true,
        ]);

        $this->assertCount(2, $member->payments);
    }
}
