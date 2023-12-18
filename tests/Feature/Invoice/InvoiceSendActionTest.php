<?php

namespace Tests\Feature\Invoice;

use App\Invoice\Actions\InvoiceSendAction;
use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Mails\BillMail;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class InvoiceSendActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanCreatePdfPayments(): void
    {
        Mail::fake();
        Tex::spy();
        Storage::fake('temp');
        $this->withoutExceptionHandling()->login()->loginNami();
        $invoice = Invoice::factory()
            ->to(ReceiverRequestFactory::new()->name('Familie Muster'))
            ->has(InvoicePosition::factory()->description('lalab')->withMember(), 'positions')
            ->via(BillKind::EMAIL)
            ->create(['mail_name' => 'Muster', 'mail_email' => 'max@muster.de']);

        InvoiceSendAction::run();

        Mail::assertSent(BillMail::class, fn ($mail) => $mail->build() && $mail->hasTo('max@muster.de', 'Muster') && Storage::disk('temp')->path('rechnung-fur-familie-muster.pdf') === $mail->filename && Storage::disk('temp')->exists('rechnung-fur-familie-muster.pdf'));
        Tex::assertCompiled(BillDocument::class, fn ($document) => 'Familie Muster' === $document->toName);
        $this->assertEquals(InvoiceStatus::SENT, $invoice->fresh()->status);
        $this->assertEquals(now()->format('Y-m-d'), $invoice->fresh()->sent_at->format('Y-m-d'));
    }
}
