<?php

namespace Tests\Feature\Payment;

use App\Member\Member;
use App\Payment\MemberPaymentBlock;
use App\Payment\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TestersBlockTest extends TestCase
{
    use DatabaseTransactions;

    public function testItHasData(): void
    {
        $this->login()->loginNami();

        Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->subscription('example', 3400))
            ->create();
        Member::factory()
            ->defaults()
            ->create();

        $data = app(MemberPaymentBlock::class)->render();

        $this->assertEquals([
            'amount' => '34,00 €',
            'members' => 1,
            'total_members' => 2,
        ], $data);
    }
}
