<?php

namespace Tests\Feature\Invoice;

use App\Invoice\Actions\InvoiceSendAction;
use App\Invoice\BillDocument;
use App\Member\Member;
use App\Payment\Payment;
use App\Payment\PaymentMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\RequestFactories\Child;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class InvoiceSendActionTest extends TestCase
{
    use DatabaseTransactions;

    public Member $member;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('temp');
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        $this->member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', [
                new Child('a', 5400),
            ]))
            ->emailBillKind()
            ->create(['firstname' => 'Lah', 'lastname' => 'Mom', 'email' => 'peter@example.com']);
    }

    public function testItCanCreatePdfPayments(): void
    {
        Mail::fake();

        Artisan::call('invoice:send');

        Mail::assertSent(PaymentMail::class, fn ($mail) => Storage::disk('temp')->path('rechnung-fur-mom.pdf') === $mail->filename && Storage::disk('temp')->exists('rechnung-fur-mom.pdf'));
    }

    public function testItCanCompileAttachment(): void
    {
        Mail::fake();
        Tex::spy();

        InvoiceSendAction::run();

        Tex::assertCompiled(BillDocument::class, fn ($document) => 'Mom' === $document->pages->first()->familyName
            && $document->pages->first()->getPositions() === ['tollerbeitrag 1997 für Lah Mom' => '54.00']
        );
    }
}
