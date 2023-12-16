<?php

namespace Tests\Feature\Invoice;

use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceIndexActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDisplaysInvoices(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        Invoice::factory()
            ->has(InvoicePosition::factory()->price(1100), 'positions')
            ->has(InvoicePosition::factory()->price(2200), 'positions')
            ->to(ReceiverRequestFactory::new()->name('Familie Blabla'))
            ->sentAt(now()->subDay())
            ->status(InvoiceStatus::SENT)
            ->create();

        $this->get(route('invoice.index'))
            ->assertInertiaPath('data.data.0.to_name', 'Familie Blabla')
            ->assertInertiaPath('data.data.0.sum_human', '33,00 €')
            ->assertInertiaPath('data.data.0.sent_at_human', now()->subDay()->format('d.m.Y'))
            ->assertInertiaPath('data.data.0.status', 'Rechnung gestellt')
            ->assertInertiaPath('data.meta.links.mass-store', route('invoice.mass-store'));
    }
}
