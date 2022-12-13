<?php

namespace Tests\Feature\Payment;

use App\Member\Member;
use App\Payment\MemberPaymentBlock;
use App\Payment\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class MemberPaymentsBlockTest extends TestCase
{
    use DatabaseTransactions;

    public function testItHasData(): void
    {
        $this->login()->loginNami();

        Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->subscription('example', [
                new Child('gg', 3400),
                new Child('gg', 100),
            ]))
            ->create();
        Member::factory()
            ->defaults()
            ->create();

        $data = app(MemberPaymentBlock::class)->render()['data'];

        $this->assertEquals([
            'amount' => '35,00 €',
            'members' => 1,
            'total_members' => 2,
        ], $data);
    }
}
