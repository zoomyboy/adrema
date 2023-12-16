<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\Invoice;
use App\Invoice\InvoiceSettings;
use App\Invoice\Queries\BillKindQuery;
use App\Invoice\Queries\InvoiceMemberQuery;
use App\Invoice\RememberDocument;
use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class BillRememberDocumentTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysMemberInformation(): void
    {
        $member = Member::factory()
            ->defaults()
            ->state([
                'firstname' => '::firstname::',
                'lastname' => '::lastname::',
                'address' => '::street::',
                'zip' => '::zip::',
                'location' => '::location::',
            ])
            ->postBillKind()
            ->has(Payment::factory()->notPaid()->nr('1995')->subscription('::subName::', [
                new Child('a', 1000),
                new Child('a', 500),
            ]))
            ->create();

        $invoice = BillDocument::fromMembers($this->query(BillDocument::class)->getMembers()->first());

        $invoice->assertHasAllContent([
            'Rechnung',
            '15.00',
            '::subName:: 1995 für ::firstname:: ::lastname::',
            'Mitgliedsbeitrag für ::lastname::',
            'Familie ::lastname::\\\\::street::\\\\::zip:: ::location::',
        ]);
    }

    public function testItDisplaysSplitPayments(): void
    {
        $member = Member::factory()
            ->defaults()
            ->postBillKind()
            ->state([
                'firstname' => '::firstname::',
                'lastname' => '::lastname::',
            ])
            ->has(Payment::factory()->notPaid()->nr('1995')->subscription('::subName::', [
                new Child('a', 1000),
                new Child('b', 500),
            ], ['split' => true]))
            ->create();

        $invoice = BillDocument::fromMembers($this->query(BillDocument::class)->getMembers()->first());

        $invoice->assertHasAllContent([
            'Rechnung',
            '10.00',
            '5.00',
            '::subName:: (a) 1995 für ::firstname:: ::lastname::',
            '::subName:: (b) 1995 für ::firstname:: ::lastname::',
            'Mitgliedsbeitrag für ::lastname::',
        ]);
    }

    public function testBillSetsFilename(): void
    {
        Member::factory()
            ->defaults()
            ->postBillKind()
            ->state(['lastname' => '::lastname::'])
            ->has(Payment::factory()->notPaid()->nr('1995'))
            ->create();

        $invoice = BillDocument::fromMembers($this->query(BillDocument::class)->getMembers()->first());

        $this->assertEquals('rechnung-fur-lastname.pdf', $invoice->compiledFilename());
    }

    public function testRememberSetsFilename(): void
    {
        Member::factory()
            ->postBillKind()
            ->defaults()
            ->state(['lastname' => '::lastname::'])
            ->has(Payment::factory()->notPaid()->state(['last_remembered_at' => now()->subMonths(6)]))
            ->create();

        $invoice = RememberDocument::fromMembers($this->query(RememberDocument::class)->getMembers()->first());

        $this->assertEquals('zahlungserinnerung-fur-lastname.pdf', $invoice->compiledFilename());
    }

    public function testItCreatesOneFileForFamilyMembers(): void
    {
        Member::factory()
            ->defaults()
            ->postBillKind()
            ->state(['firstname' => 'Max1', 'lastname' => '::lastname::', 'address' => '::address::', 'zip' => '12345', 'location' => '::location::'])
            ->has(Payment::factory()->notPaid()->nr('nr1'))
            ->create();
        Member::factory()
            ->defaults()
            ->postBillKind()
            ->state(['firstname' => 'Max2', 'lastname' => '::lastname::', 'address' => '::address::', 'zip' => '12345', 'location' => '::location::'])
            ->has(Payment::factory()->notPaid()->nr('nr2'))
            ->create();

        $this->assertCount(2, $this->query(BillDocument::class)->getMembers()->first());
    }

    /**
     * @testWith ["App\\Invoice\\BillDocument"]
     *           ["App\\Invoice\\RememberDocument"]
     */
    public function testItDisplaysSettings(string $type): void
    {
        InvoiceSettings::fake([
            'from_long' => 'langer Stammesname',
            'from' => 'Stammeskurz',
            'mobile' => '+49 176 55555',
            'email' => 'max@muster.de',
            'website' => 'https://example.com',
            'address' => 'Musterstr 4',
            'place' => 'Münster',
            'zip' => '12345',
            'iban' => 'DE444',
            'bic' => 'SOLSSSSS',
        ]);
        Member::factory()
            ->defaults()
            ->postBillKind()
            ->has(Payment::factory()->notPaid()->nr('nr2')->state(['last_remembered_at' => now()->subYear()]))
            ->create();

        $invoice = BillDocument::fromMembers($this->query(BillDocument::class)->getMembers()->first());

        $invoice->assertHasAllContent([
            'langer Stammesname',
            'Stammeskurz',
            '+49 176 55555',
            'max@muster.de',
            'https://example.com',
            'Musterstr 4',
            'Münster',
            '12345',
            'DE444',
            'SOLSSSSS',
        ]);
    }

    /**
     * @param class-string<Invoice> $type
     */
    private function query(string $type): InvoiceMemberQuery
    {
        return (new BillKindQuery(BillKind::POST))->type($type);
    }
}
