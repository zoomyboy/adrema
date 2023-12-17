<?php

namespace Tests\Feature\Payment;

use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\MemberPaymentBlock;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberPaymentBlockTest extends TestCase
{
    use DatabaseTransactions;

    public function testItHasData(): void
    {
        $this->login()->loginNami();

        $member = Member::factory()->defaults()->create();
        Member::factory()->defaults()->create();
        Invoice::factory()
            ->has(InvoicePosition::factory()->price(3500)->for($member), 'positions')
            ->has(InvoicePosition::factory()->price(1000)->for($member), 'positions')
            ->status(InvoiceStatus::SENT)->create();
        Invoice::factory()->has(InvoicePosition::factory()->price(600)->for($member), 'positions')->status(InvoiceStatus::NEW)->create();
        Invoice::factory()->has(InvoicePosition::factory()->price(1000)->for($member), 'positions')->status(InvoiceStatus::PAID)->create();

        $data = app(MemberPaymentBlock::class)->render()['data'];

        $this->assertEquals([
            'amount' => '51,00 €',
            'members' => 1,
            'total_members' => 2,
        ], $data);
    }
}
