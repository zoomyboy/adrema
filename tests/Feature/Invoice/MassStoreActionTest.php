<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillKind;
use App\Invoice\Models\Invoice;
use App\Member\Member;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class MassStoreActionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login()->loginNami()->withoutExceptionHandling();
    }

    public function testItDoesntCreatePaymentsWithoutSubscription(): void
    {
        Member::factory()->defaults()->emailBillKind()->create(['subscription_id' => null]);

        $this->postJson(route('invoice.mass-store'), [
            'year' => now()->addYear()->year,
        ])->assertOk();

        $this->assertDatabaseEmpty('invoices');
    }

    public function testItDoesntCreatePaymentWithoutBillKind(): void
    {
        Member::factory()->defaults()->create();

        $this->postJson(route('invoice.mass-store'), [
            'year' => now()->addYear()->year,
        ])->assertOk();

        $this->assertDatabaseEmpty('invoices');
    }

    public function testItCreatesPayments(): void
    {
        $member = Member::factory()->defaults()
            ->for(Subscription::factory()->forFee()->children([
                new Child('beitrag {name}', 4466),
                new Child('beitrag2 für {name} für {year}', 2290),
            ]))->emailBillKind()->create(['firstname' => 'Max', 'lastname' => 'Muster', 'address' => 'Maxstr 4', 'zip' => '33445', 'location' => 'Solingen', 'email' => 'lala@b.de']);

        $this->postJson(route('invoice.mass-store'), [
            'year' => now()->addYear()->year,
        ])->assertOk();

        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertEquals([
            'name' => 'Familie Muster',
            'address' => 'Maxstr 4',
            'zip' => '33445',
            'location' => 'Solingen',
        ], $invoice->to);
        $this->assertEquals('Mitgliedsbeitrag für Muster', $invoice->usage);
        $this->assertEquals('lala@b.de', $invoice->mail_email);
        $this->assertEquals(BillKind::EMAIL, $invoice->via);
        $this->assertDatabaseHas('invoice_positions', [
            'invoice_id' => $invoice->id,
            'member_id' => $member->id,
            'price' => 4466,
            'description' => 'beitrag Max Muster'
        ]);
        $this->assertDatabaseHas('invoice_positions', [
            'invoice_id' => $invoice->id,
            'member_id' => $member->id,
            'price' => 2290,
            'description' => 'beitrag2 für Max Muster für ' . now()->addYear()->year
        ]);
    }

    public function testItCreatesOneInvoiceForFamilyMember(): void
    {
        $subscription = Subscription::factory()->forFee()->children([new Child('beitrag {name}', 4466)])->create();
        $member = Member::factory()->defaults()->for($subscription)->emailBillKind()->create(['firstname' => 'Max', 'lastname' => 'Muster']);
        Member::factory()->defaults()->for($subscription)->sameFamilyAs($member)->emailBillKind()->create(['firstname' => 'Jane']);

        $this->postJson(route('invoice.mass-store'), ['year' => now()->addYear()->year])->assertOk();

        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseCount('invoice_positions', 2);
        $this->assertDatabaseHas('invoice_positions', ['description' => 'beitrag Max Muster']);
        $this->assertDatabaseHas('invoice_positions', ['description' => 'beitrag Jane Muster']);
    }

    public function testItSeparatesBillKinds(): void
    {
        $subscription = Subscription::factory()->forFee()->children([new Child('beitrag {name]', 4466)])->create();
        $member = Member::factory()->defaults()->for($subscription)->emailBillKind()->create();
        Member::factory()->defaults()->for($subscription)->sameFamilyAs($member)->postBillKind()->create();

        $this->postJson(route('invoice.mass-store'), ['year' => now()->addYear()->year])->assertOk();

        $this->assertDatabaseCount('invoices', 2);
        $this->assertDatabaseCount('invoice_positions', 2);
    }
}
