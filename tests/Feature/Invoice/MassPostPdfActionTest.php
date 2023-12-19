<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MassPostPdfActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDoesntDisplayPdfWhenNoMembersFound(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        $this->get(route('invoice.masspdf'))->assertStatus(204);
    }

    public function testItDoesntDisplayPdfWhenAllInvoicesPaid(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        Invoice::factory()->has(InvoicePosition::factory()->withMember(), 'positions')->via(BillKind::POST)->status(InvoiceStatus::PAID)->create();

        $this->get(route('invoice.masspdf'))->assertStatus(204);
    }

    public function testItDoesntDisplayEmailBills(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        Invoice::factory()->has(InvoicePosition::factory()->withMember(), 'positions')->via(BillKind::EMAIL)->create();

        $this->get(route('invoice.masspdf'))->assertStatus(204);
    }

    public function testItMergesRememberAndBill(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $invoice1 = Invoice::factory()->has(InvoicePosition::factory()->withMember(), 'positions')->status(InvoiceStatus::NEW)
            ->via(BillKind::POST)
            ->create();
        $invoice2 = Invoice::factory()->has(InvoicePosition::factory()->withMember(), 'positions')->status(InvoiceStatus::SENT)
            ->via(BillKind::POST)
            ->create(['sent_at' => now()->subMonths(10), 'last_remembered_at' => now()->subMonths(4)]);

        $this->get(route('invoice.masspdf'))->assertPdfPageCount(2);

        $this->assertEquals(InvoiceStatus::SENT, $invoice1->fresh()->status);
        $this->assertEquals(now()->format('Y-m-d'), $invoice1->fresh()->last_remembered_at->format('Y-m-d'));
        $this->assertEquals(now()->format('Y-m-d'), $invoice1->fresh()->sent_at->format('Y-m-d'));

        $this->assertEquals(InvoiceStatus::SENT, $invoice2->fresh()->status);
        $this->assertEquals(now()->format('Y-m-d'), $invoice2->fresh()->last_remembered_at->format('Y-m-d'));
        $this->assertEquals(now()->subMonths(10)->format('Y-m-d'), $invoice2->fresh()->sent_at->format('Y-m-d'));
    }
}
