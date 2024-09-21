<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InvoiceUpdateActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItCanUpdateAnInvoice(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $invoice = Invoice::factory()->create();

        $this->patchJson(
            route('invoice.update', ['invoice' => $invoice]),
            InvoiceRequestFactory::new()
                ->to(ReceiverRequestFactory::new()->name('Familie Blabla')->address('Musterstr 44')->zip('22222')->location('Solingen'))
                ->status(InvoiceStatus::PAID)
                ->via(BillKind::POST)
                ->state([
                    'greeting' => 'Hallo Familie',
                ])
                ->create()
        )->assertOk();

        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseHas('invoices', [
            'greeting' => 'Hallo Familie',
            'via' => BillKind::POST->value,
            'status' => InvoiceStatus::PAID->value,
            'id' => $invoice->id,
        ]);
        $invoice = Invoice::firstWhere('greeting', 'Hallo Familie');
        $this->assertEquals([
            'name' => 'Familie Blabla',
            'address' => 'Musterstr 44',
            'zip' => '22222',
            'location' => 'Solingen',
        ], $invoice->to);
    }

    public function testItAddsAPosition(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $invoice = Invoice::factory()->create();

        $this->patchJson(
            route('invoice.update', ['invoice' => $invoice]),
            InvoiceRequestFactory::new()
                ->position(InvoicePositionRequestFactory::new())
                ->position(InvoicePositionRequestFactory::new())
                ->create()
        )->assertOk();

        $this->assertDatabaseCount('invoice_positions', 2);
    }

    public function testItUpdatesAPosition(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $invoice = Invoice::factory()->has(InvoicePosition::factory()->withMember(), 'positions')->create();

        $this->patchJson(
            route('invoice.update', ['invoice' => $invoice]),
            InvoiceRequestFactory::new()
                ->position(InvoicePositionRequestFactory::new()->description('la')->id($invoice->positions->first()->id))
                ->create()
        )->assertOk();

        $this->assertDatabaseCount('invoice_positions', 1);
        $this->assertDatabaseHas('invoice_positions', [
            'description' => 'la',
            'id' => $invoice->positions->first()->id,
        ]);
    }

    public function testItDeletesAPosition(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $invoice = Invoice::factory()->has(InvoicePosition::factory()->withMember(), 'positions')->create();

        $this->patchJson(
            route('invoice.update', ['invoice' => $invoice]),
            InvoiceRequestFactory::new()
                ->create()
        )->assertOk();

        $this->assertDatabaseCount('invoice_positions', 0);
    }

    public static function validationDataProvider(): Generator
    {
        yield [
            ['to.address' => ''],
            ['to.address' => 'Adresse ist erforderlich.']
        ];

        yield [
            ['to.name' => ''],
            ['to.name' => 'Name ist erforderlich.']
        ];

        yield [
            ['to.location' => ''],
            ['to.location' => 'Ort ist erforderlich.']
        ];

        yield [
            ['status' => ''],
            ['status' => 'Status ist erforderlich.']
        ];

        yield [
            ['status' => 'lala'],
            ['status' => 'Der gewählte Wert für Status ist ungültig.']
        ];

        yield [
            ['to.zip' => ''],
            ['to.zip' => 'PLZ ist erforderlich.']
        ];

        yield [
            ['via' => ''],
            ['via' => 'Rechnungsweg ist erforderlich.']
        ];

        yield [
            ['via' => 'lala'],
            ['via' => 'Der gewählte Wert für Rechnungsweg ist ungültig.']
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, string> $errors
     */
    #[DataProvider('validationDataProvider')]
    public function testItValidatesInput(array $input, array $errors): void
    {
        $this->login()->loginNami();

        $response = $this->postJson(
            route('invoice.store'),
            InvoiceRequestFactory::new()
                ->to(ReceiverRequestFactory::new())
                ->position(InvoicePositionRequestFactory::new()->member(Member::factory()->defaults()->create()))
                ->via(BillKind::POST)
                ->state($input)
                ->create()
        );

        $response->assertJsonValidationErrors($errors);
    }
}
