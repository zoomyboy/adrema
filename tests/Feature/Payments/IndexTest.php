<?php

namespace Tests\Feature\Payments;

use App\Member\Member;
use App\Pdf\BillType;
use Database\Factories\Payment\PaymentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use RefreshDatabase;

    public function testItGetsLinkWhenMemberHasPayableBills(): void
    {
        $this->withoutExceptionHandling();
        $this->login();

        $member = Member::factory()
            ->defaults()
            ->withPayments([
                fn (PaymentFactory $payment) => $payment->notPaid()->nr('1995')->subscription('::subName::', 1500),
            ])
            ->create(['firstname' => '::firstname']);

        $this->get("/member/{$member->id}/payment")->assertInertia('member/Index', [
            'href' => route('member.singlepdf', ['member' => $member, 'type' => BillType::class]),
            'label' => 'Rechnung erstellen',
            'disabled' => false,
        ], 'single.payment_links.0');
    }

    public function testLinkIsFalseWhenMemberHasnoPayments(): void
    {
        $this->withoutExceptionHandling();
        $this->login();

        $member = Member::factory()
            ->defaults()
            ->create(['firstname' => '::firstname']);

        $this->get("/member/{$member->id}/payment")->assertInertia('member/Index', [
            'href' => route('member.singlepdf', ['member' => $member, 'type' => BillType::class]),
            'label' => 'Rechnung erstellen',
            'disabled' => true,
        ], 'single.payment_links.0');
    }

    public function testItReturnsDisabledWhenPaymentsArePaid(): void
    {
        $this->withoutExceptionHandling();
        $this->login();

        $member = Member::factory()
            ->defaults()
            ->withPayments([
                fn (PaymentFactory $payment) => $payment->paid()->nr('1995')->subscription('::subName::', 1500),
            ])
            ->create(['firstname' => '::firstname']);

        $this->get("/member/{$member->id}/payment")->assertInertia('member/Index', [
            'href' => route('member.singlepdf', ['member' => $member, 'type' => BillType::class]),
            'label' => 'Rechnung erstellen',
            'disabled' => true,
        ], 'single.payment_links.0');
    }

}
