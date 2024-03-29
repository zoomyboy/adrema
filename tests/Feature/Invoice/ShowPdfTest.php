<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\InvoiceSettings;
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
        $this->login()->loginNami();
        $invoice = Invoice::factory()
            ->to(ReceiverRequestFactory::new()->name('Familie Lala'))
            ->has(InvoicePosition::factory()->withMember()->price(1500)->description('Beitrag12'), 'positions')
            ->via(BillKind::EMAIL)
            ->create(['usage' => 'Usa']);

        $this->get(route('invoice.pdf', ['invoice' => $invoice]))
            ->assertOk()
            ->assertPdfPageCount(1)
            ->assertPdfName('rechnung-fur-familie-lala.pdf');

        Tex::assertCompiled(BillDocument::class, fn ($document) => $document->hasAllContent([
            'Beitrag12',
            'Familie Lala',
            'Rechnung',
            '15.00',
            'Usa',
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
        ]));
    }
}
