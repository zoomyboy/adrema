<?php

namespace Tests\Feature\Sendpayment;

use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\InvoiceSettings;
use App\Invoice\Queries\BillKindQuery;
use App\Invoice\RememberDocument;
use App\Member\Member;
use App\Payment\Payment;
use App\Payment\Status;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\RequestFactories\InvoiceSettingsFake;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class SendpaymentTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanViewSendpaymentPage(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->loginNami();

        $response = $this->get(route('sendpayment.create'));

        $response->assertOk();
        $this->assertInertiaHas('Rechnungen versenden', $response, 'types.0.link.label');
        $href = $this->inertia($response, 'types.0.link.href');
        $this->assertStringContainsString('BillDocument', $href);
    }

    public function testItDownloadsPdfOfAllMembersForBill(): void
    {
        InvoiceSettings::fake(InvoiceSettingsFake::new()->create());
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->postBillKind()->count(3)
            ->has(Payment::factory()->notPaid()->subscription('tollerbeitrag', [new Child('a', 5400)]))
            ->create();

        $response = $this->call('GET', route('sendpayment.pdf'), ['type' => 'App\\Invoice\\BillDocument']);
        $response->assertOk();
        $this->assertPdfPageCount(3, $response->getFile());
    }

    public function testItDownloadsPdfOfAllMembersForRemember(): void
    {
        InvoiceSettings::fake(InvoiceSettingsFake::new()->create());
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->postBillKind()->count(3)
            ->has(Payment::factory()->pending()->subscription('tollerbeitrag', [new Child('a', 5400)]))
            ->create();

        $response = $this->call('GET', route('sendpayment.pdf'), ['type' => 'App\\Invoice\\RememberDocument']);
        $response->assertOk();
        $this->assertPdfPageCount(3, $response->getFile());
    }

    public function testItCanCreatePdfPayments(): void
    {
        InvoiceSettings::fake(InvoiceSettingsFake::new()->create());
        Tex::spy();
        $this->withoutExceptionHandling()->login()->loginNami();
        $members = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', [new Child('a', 5400)]))
            ->has(Payment::factory()->paid()->nr('1998')->subscription('bezahltdesc', [new Child('b', 5800)]))
            ->postBillKind()
            ->count(3)
            ->create();
        $member = $members->first();

        $this->call('GET', route('sendpayment.pdf'), ['type' => 'App\\Invoice\\BillDocument']);
        $this->assertEquals(Status::firstWhere('name', 'Rechnung gestellt')->id, $member->payments->firstWhere('nr', '1997')->status_id);
        $this->assertEquals(Status::firstWhere('name', 'Rechnung beglichen')->id, $member->payments->firstWhere('nr', '1998')->status_id);
        Tex::assertCompiled(
            BillDocument::class,
            fn ($document) => $document->hasAllContent(['1997', 'tollerbeitrag', '54.00'])
                && $document->missesAllContent(['1998', 'bezahltdesc', '58.00'])
        );

        $member->payments->firstWhere('nr', '1997')->update(['status_id' => Status::firstWhere('name', 'Nicht bezahlt')->id]);
        $invoice = BillDocument::fromMembers((new BillKindQuery(BillKind::POST))->type(BillDocument::class)->getMembers()->first());
        $this->assertEquals(
            BillDocument::from($member->payments->firstWhere('nr', '1997')->invoice_data)->renderBody(),
            $invoice->renderBody()
        );
    }

    public function testItCanCreatePdfPaymentsForRemember(): void
    {
        InvoiceSettings::fake(InvoiceSettingsFake::new()->create());
        Tex::spy();
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->pending()->nr('1997')->subscription('tollerbeitrag', [new Child('a', 5400)]))
            ->postBillKind()
            ->create();

        $this->call('GET', route('sendpayment.pdf'), ['type' => 'App\\Invoice\\RememberDocument']);
        Tex::assertCompiled(
            RememberDocument::class,
            fn ($document) => $document->hasAllContent(['1997', 'tollerbeitrag', '54.00'])
        );
        $this->assertNull($member->payments()->first()->invoice_data);
        $this->assertEquals(now()->format('Y-m-d'), $member->payments->first()->last_remembered_at->format('Y-m-d'));
    }

    public function testItDoesntCreatePdfsWhenUserHasEmail(): void
    {
        Tex::spy();
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', [new Child('u', 5400)]))
            ->emailBillKind()
            ->create();

        $response = $this->call('GET', route('sendpayment.pdf'), ['type' => 'App\\Invoice\\BillDocument']);

        $response->assertStatus(204);
        Tex::assertNotCompiled(BillDocument::class);
    }
}
