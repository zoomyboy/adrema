<?php

namespace Tests\Feature\Payment;

use App\Invoice\BillDocument;
use App\Invoice\DocumentFactory;
use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class PaymentPdfTest extends TestCase
{

    use DatabaseTransactions;

    public function testItShowsAnInvoiceAsPdf(): void
    {
        $this->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', [
                new Child('a', 5400),
            ]))
            ->emailBillKind()
            ->create(['firstname' => 'Lah', 'lastname' => 'Mom', 'email' => 'peter@example.com']);
        /** @var Collection<(int|string), Member> */
        $members = collect([$member]);
        app(DocumentFactory::class)->afterSingle(BillDocument::fromMembers($members), $members);

        $response = $this->get(route('payment.pdf', ['payment' => $member->payments->first()]));
        $response->assertOk();
        $this->assertPdfPageCount(1, $response->getFile());
    }

    public function testItReturnsNoPdfWhenPaymentDoesntHaveInvoiceData(): void
    {
        $this->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', [
                new Child('a', 5400),
            ]))
            ->emailBillKind()
            ->create(['firstname' => 'Lah', 'lastname' => 'Mom', 'email' => 'peter@example.com']);
        /** @var Collection<(int|string), Member> */

        $response = $this->get(route('payment.pdf', ['payment' => $member->payments->first()]));
        $response->assertStatus(204);
    }
}
