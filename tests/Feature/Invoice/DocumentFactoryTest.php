<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillDocument;
use App\Invoice\DocumentFactory;
use App\Invoice\InvoiceSettings;
use App\Invoice\Queries\InvoiceMemberQuery;
use App\Invoice\Queries\SingleMemberQuery;
use App\Invoice\RememberDocument;
use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class DocumentFactoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @testWith ["\\App\\Invoice\\BillDocument"]
     *           ["\\App\\Invoice\\RememberDocument"]
     */
    public function testItDoesntReturnARepositoryWhenMemberDoesntHavePayments(): void
    {
        $member = Member::factory()->defaults()->create();
        $invoice = app(DocumentFactory::class)->singleInvoice(BillDocument::class, $this->query($member));
        $this->assertNull($invoice);
    }

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
            ->has(Payment::factory()->notPaid()->nr('1995')->subscription('::subName::', [
                new Child('a', 1000),
                new Child('a', 500),
            ]))
            ->create();

        $invoice = app(DocumentFactory::class)->singleInvoice(BillDocument::class, $this->query($member));

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
            ->state([
                'firstname' => '::firstname::',
                'lastname' => '::lastname::',
            ])
            ->has(Payment::factory()->notPaid()->nr('1995')->subscription('::subName::', [
                new Child('a', 1000),
                new Child('b', 500),
            ], ['split' => true]))
            ->create();

        $invoice = app(DocumentFactory::class)->singleInvoice(BillDocument::class, $this->query($member));

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
        $member = Member::factory()
            ->defaults()
            ->state(['lastname' => '::lastname::'])
            ->has(Payment::factory()->notPaid()->nr('1995'))
            ->create();

        $invoice = app(DocumentFactory::class)->singleInvoice(BillDocument::class, $this->query($member));

        $this->assertEquals('rechnung-fur-lastname.pdf', $invoice->compiledFilename());
    }

    public function testRememberSetsFilename(): void
    {
        $member = Member::factory()
            ->defaults()
            ->state(['lastname' => '::lastname::'])
            ->has(Payment::factory()->notPaid())
            ->create();

        $invoice = app(DocumentFactory::class)->singleInvoice(RememberDocument::class, $this->query($member));

        $this->assertEquals('zahlungserinnerung-fur-lastname.pdf', $invoice->compiledFilename());
    }

    public function testItCreatesOneFileForFamilyMembers(): void
    {
        $firstMember = Member::factory()
            ->defaults()
            ->state(['firstname' => 'Max1', 'lastname' => '::lastname::', 'address' => '::address::', 'zip' => '12345', 'location' => '::location::'])
            ->has(Payment::factory()->notPaid()->nr('nr1'))
            ->create();
        Member::factory()
            ->defaults()
            ->state(['firstname' => 'Max2', 'lastname' => '::lastname::', 'address' => '::address::', 'zip' => '12345', 'location' => '::location::'])
            ->has(Payment::factory()->notPaid()->nr('nr2'))
            ->create();

        $invoice = app(DocumentFactory::class)->singleInvoice(BillDocument::class, $this->query($firstMember));

        $invoice->assertHasAllContent(['Max1', 'Max2', 'nr1', 'nr2']);
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
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('nr2'))
            ->create();

        $invoice = app(DocumentFactory::class)->singleInvoice($type, $this->query($member));

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

    public function testItGeneratesAPdf(): void
    {
        Tex::fake();
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid())
            ->create(['lastname' => 'lastname']);
        $this->withoutExceptionHandling();
        $this->login()->init()->loginNami();

        $response = $this->call('GET', "/member/{$member->id}/pdf", [
            'type' => BillDocument::class,
        ]);

        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertEquals('inline; filename="rechnung-fur-lastname.pdf"', $response->headers->get('content-disposition'));
    }

    private function query(Member $member): InvoiceMemberQuery
    {
        return new SingleMemberQuery($member);
    }
}
