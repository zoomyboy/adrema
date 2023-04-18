<?php

namespace Tests\Feature\Sendpayment;

use App\Invoice\BillDocument;
use App\Invoice\InvoiceSettings;
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

    public function testItCanCreatePdfPayments(): void
    {
        InvoiceSettings::fake(InvoiceSettingsFake::new()->create());
        Tex::spy();
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', [new Child('a', 5400)]))
            ->has(Payment::factory()->paid()->nr('1998')->subscription('bezahltdesc', [new Child('b', 5800)]))
            ->postBillKind()
            ->create();

        $response = $this->call('GET', route('sendpayment.pdf'), ['type' => 'App\\Invoice\\BillDocument']);

        $response->assertOk();
        $this->assertEquals(Status::firstWhere('name', 'Rechnung gestellt')->id, $member->payments->firstWhere('nr', '1997')->status_id);
        $this->assertEquals(Status::firstWhere('name', 'Rechnung beglichen')->id, $member->payments->firstWhere('nr', '1998')->status_id);
        Tex::assertCompiled(BillDocument::class, fn ($document) => $document->hasAllContent(['1997', 'tollerbeitrag', '54.00'])
            && $document->missesAllContent(['1998', 'bezahltdesc', '58.00'])
        );
    }

    public function testItDoesntCreatePdfsWhenUserHasEmail(): void
    {
        Tex::spy();
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', [new Child('u', 5400)]))
            ->emailBillKind()
            ->create();

        $response = $this->call('GET', route('sendpayment.pdf'), ['type' => 'App\\Invoice\\BillDocument']);

        $response->assertStatus(204);
        Tex::assertNotCompiled(BillDocument::class);
    }
}
