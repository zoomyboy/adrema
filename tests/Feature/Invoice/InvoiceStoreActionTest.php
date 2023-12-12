<?php

namespace Tests\Feature\Invoice;

use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Member\Member;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceStoreActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItCanCreateAnInvoice(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $member = Member::factory()->defaults()->create();

        $response = $this->postJson(
            route('invoice.store'),
            InvoiceRequestFactory::new()
                ->to(ReceiverRequestFactory::new()->name('Familie Blabla')->address('Musterstr 44')->zip('22222')->location('Solingen'))
                ->position(InvoicePositionRequestFactory::new()->description('Beitrag Abc')->price(3250)->member($member))
                ->status(InvoiceStatus::PAID)
                ->state([
                    'greeting' => 'Hallo Familie',
                    'intro' => 'Hiermit stellen wir ihnen den Beitrag in Rechnung.',
                    'outro' => 'Das ist die Rechnung',
                ])
                ->create()
        );

        $response->assertOk();
        $this->assertDatabaseHas('invoices', [
            'greeting' => 'Hallo Familie',
            'intro' => 'Hiermit stellen wir ihnen den Beitrag in Rechnung.',
            'outro' => 'Das ist die Rechnung',
            'status' => InvoiceStatus::PAID->value,
        ]);
        $invoice = Invoice::firstWhere('greeting', 'Hallo Familie');
        $this->assertDatabaseHas('invoice_positions', [
            'invoice_id' => $invoice->id,
            'member_id' => $member->id,
            'price' => 3250,
            'description' => 'Beitrag Abc',
        ]);
        $this->assertEquals([
            'name' => 'Familie Blabla',
            'address' => 'Musterstr 44',
            'zip' => '22222',
            'location' => 'Solingen',
        ], $invoice->to);
    }

    public function validationDataProvider(): Generator
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
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, string> $errors
     * @dataProvider validationDataProvider
     */
    public function testItValidatesInput(array $input, array $errors): void
    {
        $this->login()->loginNami();

        $response = $this->postJson(
            route('invoice.store'),
            InvoiceRequestFactory::new()
                ->to(ReceiverRequestFactory::new())
                ->position(InvoicePositionRequestFactory::new()->member(Member::factory()->defaults()->create()))
                ->state($input)
                ->create()
        );

        $response->assertJsonValidationErrors($errors);
    }
}
