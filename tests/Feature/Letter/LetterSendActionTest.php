<?php

namespace Tests\Feature\Letter;

use App\Letter\Actions\LetterSendAction;
use App\Letter\BillDocument;
use App\Member\Member;
use App\Payment\Payment;
use App\Payment\PaymentMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Mail;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class LetterSendActionTest extends TestCase
{
    use DatabaseTransactions;

    public Member $member;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        $this->member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', 5400))
            ->emailBillKind()
            ->create(['firstname' => 'Lah', 'lastname' => 'Mom', 'email' => 'peter@example.com']);
    }

    public function testItCanCreatePdfPayments(): void
    {
        Mail::fake();

        app(LetterSendAction::class)->handle();

        Mail::assertSent(PaymentMail::class, fn ($mail) => Storage::path('rechnung-fur-mom.pdf') === $mail->filename);
    }

    public function testItCanCompileAttachment(): void
    {
        Mail::fake();
        Tex::spy();

        LetterSendAction::run();

        Tex::assertCompiled(BillDocument::class, fn ($document) => 'Mom' === $document->pages->first()->familyName
            && $document->pages->first()->getPositions() === ['Beitrag 1997 für Lah Mom (tollerbeitrag)' => '54.00']
        );
    }
}
