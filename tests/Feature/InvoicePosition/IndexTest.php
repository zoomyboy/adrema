<?php

namespace Tests\Feature\InvoicePosition;

use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use DatabaseTransactions;

    public function testItShowsInvoicePositions(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->has(InvoicePosition::factory()->for(Invoice::factory()->status(InvoiceStatus::SENT))->description('lala b')->price(5566))
            ->defaults()->create();

        $this->get(route('member.invoice-position.index', ['member' => $member]))
            ->assertJsonPath('data.0.description', 'lala b')
            ->assertJsonPath('data.0.price_human', '55,66 €')
            ->assertJsonPath('data.0.id', $member->invoicePositions->first()->id)
            ->assertJsonPath('data.0.invoice.status', 'Rechnung gestellt');
    }
}
