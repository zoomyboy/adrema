<?php

namespace Tests\Feature\Invoice;

use App\Member\Member;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class MemberNewInvoiceActionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login()->loginNami()->withoutExceptionHandling();
    }

    public function testItReturnsNewInvoiceOfMember(): void
    {
        $subscription = Subscription::factory()->children([
            new Child('beitrag {name}', 4466),
            new Child('beitrag2 für {name} für {year}', 2290),
        ])->create();
        $member = Member::factory()
            ->defaults()
            ->emailBillKind()
            ->create(['firstname' => 'Max', 'lastname' => 'Muster', 'address' => 'Maxstr 4', 'zip' => '33445', 'location' => 'Solingen', 'email' => 'lala@b.de']);

        $this->post(route('invoice.new-invoice-attributes'), ['member_id' => $member->id, 'year' => 2019, 'subscription_id' => $subscription->id])
            ->assertOk()
            ->assertJsonPath('greeting', 'Liebe Familie Muster')
            ->assertJsonPath('to.address', 'Maxstr 4')
            ->assertJsonPath('to.location', 'Solingen')
            ->assertJsonPath('to.zip', '33445')
            ->assertJsonPath('to.name', 'Familie Muster')
            ->assertJsonPath('usage', 'Mitgliedsbeitrag für Muster')
            ->assertJsonPath('via', 'E-Mail')
            ->assertJsonPath('mail_email', 'lala@b.de')
            ->assertJsonPath('status', 'Neu')
            ->assertJsonPath('positions.0.description', 'beitrag Max Muster')
            ->assertJsonPath('positions.0.member_id', $member->id)
            ->assertJsonPath('positions.0.price', 4466)
            ->assertJsonPath('positions.1.description', 'beitrag2 für Max Muster für 2019')
            ->assertJsonPath('positions.1.member_id', $member->id)
            ->assertJsonPath('positions.1.price', 2290);

        $this->assertDatabaseCount('invoices', 0);
        $this->assertDatabaseCount('invoice_positions', 0);
    }
}
