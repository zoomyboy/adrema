<?php

namespace Tests\Feature\Letter;

use App\Letter\Actions\LetterSendAction;
use App\Letter\BillDocument;
use App\Member\Member;
use App\Payment\Payment;
use App\Payment\PaymentMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Mail;
use Tests\RequestFactories\Child;
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
            ->has(Payment::factory()->notPaid()->nr('1997')->subscription('tollerbeitrag', [
                new Child('a', 5400),
            ]))
            ->emailBillKind()
            ->create(['firstname' => 'Lah', 'lastname' => 'Mom', 'email' => 'peter@example.com']);
    }

    public function testItCanCreatePdfPayments(): void
    {
        Mail::fake();

        Artisan::call('letter:send');

        Mail::assertSent(PaymentMail::class, fn ($mail) => Storage::path('rechnung-fur-mom.pdf') === $mail->filename);
    }

    public function testItCanCompileAttachment(): void
    {
        Mail::fake();
        Tex::spy();

        LetterSendAction::run();

        Tex::assertCompiled(BillDocument::class, fn ($document) => 'Mom' === $document->pages->first()->familyName
            && $document->pages->first()->getPositions() === ['tollerbeitrag 1997 für Lah Mom' => '54.00']
        );
    }
}
