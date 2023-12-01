<?php

namespace Tests\Feature\Payment;

use App\Invoice\BillDocument;
use App\Invoice\DocumentFactory;
use App\Member\Member;
use App\Payment\Payment;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use DatabaseTransactions;

    public function testItShowsPayments(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->has(Payment::factory()->notPaid()->nr('2019')->subscription('Free', [
                new Child('a', 1000),
                new Child('b', 50),
            ]))
            ->defaults()->create();
        $payment = $member->payments->first();

        $this->get("/member/{$member->id}/payment")
            ->assertJsonPath('data.0.subscription.name', 'Free')
            ->assertJsonPath('data.0.subscription.id', $payment->subscription->id)
            ->assertJsonPath('data.0.subscription.amount', 1050)
            ->assertJsonPath('data.0.subscription_id', $payment->subscription->id)
            ->assertJsonPath('data.0.status_name', 'Nicht bezahlt')
            ->assertJsonPath('data.0.nr', '2019')
            ->assertJsonPath('data.0.links.show', null)
            ->assertJsonPath('data.0.links.update', url("/payment/{$payment->id}"))
            ->assertJsonPath('data.0.links.destroy', url("/payment/{$payment->id}"))
            ->assertJsonPath('meta.statuses.0.name', 'Nicht bezahlt')
            ->assertJsonPath('meta.statuses.0.id', $payment->status->id)
            ->assertJsonPath('meta.subscriptions.0.id', Subscription::first()->id)
            ->assertJsonPath('meta.subscriptions.0.name', Subscription::first()->name)
            ->assertJsonPath('meta.links.store', url("/member/{$member->id}/payment"));
    }

    public function testItShowsPaymentLink(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->has(Payment::factory()->notPaid()->nr('2019')->subscription('Free', [
                new Child('a', 1000),
                new Child('b', 50),
            ]))
            ->defaults()->create();

        $members = collect([$member]);
        app(DocumentFactory::class)->afterSingle(BillDocument::fromMembers($members), $members);

        $this->get("/member/{$member->id}/payment")
            ->assertJsonPath('data.0.links.show', route('payment.pdf', ['payment' => $member->payments->first()]));
    }
}
