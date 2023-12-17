<?php

namespace Tests\Feature\Invoice;

use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceDestroyActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDestroysInvoice(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $invoice = Invoice::factory()->has(InvoicePosition::factory()->withMember(), 'positions')->create();

        $this->delete(route('invoice.destroy', ['invoice' => $invoice]))->assertOk();
        $this->assertDatabaseCount('invoices', 0);
        $this->assertDatabaseCount('invoice_positions', 0);
    }
}
