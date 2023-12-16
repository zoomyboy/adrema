<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillKind;
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
            ->via(BillKind::POST)
            ->status(InvoiceStatus::SENT)
            ->create();

        $this->get(route('invoice.index'))
            ->assertInertiaPath('data.data.0.to_name', 'Familie Blabla')
            ->assertInertiaPath('data.data.0.sum_human', '33,00 €')
            ->assertInertiaPath('data.data.0.sent_at_human', now()->subDay()->format('d.m.Y'))
            ->assertInertiaPath('data.data.0.status', 'Rechnung gestellt')
            ->assertInertiaPath('data.data.0.via', 'Post')
            ->assertInertiaPath('data.meta.links.mass-store', route('invoice.mass-store'))
            ->assertInertiaPath('data.meta.links.store', route('invoice.store'))
            ->assertInertiaPath('data.meta.vias.0', ['id' => 'E-Mail', 'name' => 'E-Mail'])
            ->assertInertiaPath('data.meta.statuses.0', ['id' => 'Neu', 'name' => 'Neu'])
            ->assertInertiaPath('data.meta.default', [
                'to' => [
                    'name' => '',
                    'address' => '',
                    'zip' => '',
                    'location' => '',
                ],
                'positions' => [],
                'greeting' => '',
                'status' => InvoiceStatus::NEW->value,
                'via' => null,
            ])
            ->assertInertiaPath('data.meta.default_position', [
                'price' => 0,
                'description' => '',
            ]);
    }

    public function testValuesCanBeNull(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        Invoice::factory()->create();

        $this->get(route('invoice.index'))
            ->assertInertiaPath('data.data.0.sent_at_human', '');
    }
}
