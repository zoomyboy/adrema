<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class ShowPdfTest extends TestCase
{

    use DatabaseTransactions;

    public function testItShowsAnInvoiceAsPdf(): void
    {
        Tex::spy();
        $this->login()->loginNami();
        $invoice = Invoice::factory()
            ->to(ReceiverRequestFactory::new()->name('Familie Lala'))
            ->has(InvoicePosition::factory()->withMember()->description('Beitrag12'), 'positions')
            ->via(BillKind::EMAIL)
            ->create();

        $this->get(route('invoice.pdf', ['invoice' => $invoice]))
            ->assertOk()
            ->assertPdfPageCount(1)
            ->assertPdfName('rechnung-fur-familie-lala.pdf');

        Tex::assertCompiled(BillDocument::class, fn ($document) => $document->hasAllContent(['Beitrag12', 'Familie Lala']));
    }
}
