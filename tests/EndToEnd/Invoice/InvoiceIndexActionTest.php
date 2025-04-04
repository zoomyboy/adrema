<?php

namespace Tests\Feature\EndToEnd;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\EndToEndTestCase;
use Tests\Feature\Invoice\ReceiverRequestFactory;

uses(DatabaseTransactions::class);
uses(EndToEndTestCase::class);

it('testItDisplaysInvoices', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $subscription = Subscription::factory()->forFee()->name('Beitrag')->create();
    $member = Member::factory()->defaults()->create(['firstname' => 'Aaaa', 'lastname' => 'Aaab']);
    $invoice = Invoice::factory()
        ->has(InvoicePosition::factory()->price(1100)->for($member)->state(['description' => 'lala']), 'positions')
        ->has(InvoicePosition::factory()->price(2200)->withMember(), 'positions')
        ->to(ReceiverRequestFactory::new()->name('Familie Blabla'))
        ->sentAt(now()->subDay())
        ->via(BillKind::POST)
        ->status(InvoiceStatus::SENT)
        ->create(['usage' => 'Usa', 'mail_email' => 'a@b.de']);

    sleep(2);
    test()->get(route('invoice.index'))
        ->assertInertiaPath('data.data.0.to.name', 'Familie Blabla')
        ->assertInertiaPath('data.data.0.id', $invoice->id)
        ->assertInertiaPath('data.data.0.sum_human', '33,00 €')
        ->assertInertiaPath('data.data.0.sent_at_human', now()->subDay()->format('d.m.Y'))
        ->assertInertiaPath('data.data.0.status', 'Rechnung gestellt')
        ->assertInertiaPath('data.data.0.via', 'Post')
        ->assertInertiaPath('data.data.0.mail_email', 'a@b.de')
        ->assertInertiaPath('data.data.0.usage', 'Usa')
        ->assertInertiaPath('data.data.0.greeting', $invoice->greeting)
        ->assertInertiaPath('data.data.0.positions.0.price', 1100)
        ->assertInertiaPath('data.data.0.positions.0.member_id', $member->id)
        ->assertInertiaPath('data.data.0.positions.0.description', 'lala')
        ->assertInertiaPath('data.data.0.positions.0.id', $invoice->positions->first()->id)
        ->assertInertiaPath('data.data.0.links.pdf', route('invoice.pdf', ['invoice' => $invoice]))
        ->assertInertiaPath('data.data.0.links.rememberpdf', route('invoice.rememberpdf', ['invoice' => $invoice]))
        ->assertInertiaPath('data.data.0.links.update', route('invoice.update', ['invoice' => $invoice]))
        ->assertInertiaPath('data.data.0.links.destroy', route('invoice.destroy', ['invoice' => $invoice]))
        ->assertInertiaPath('data.meta.links.mass-store', route('invoice.mass-store'))
        ->assertInertiaPath('data.meta.links.newInvoiceAttributes', route('invoice.new-invoice-attributes'))
        ->assertInertiaPath('data.meta.links.store', route('invoice.store'))
        ->assertInertiaPath('data.meta.links.masspdf', route('invoice.masspdf'))
        ->assertInertiaPath('data.meta.vias.0', ['id' => 'E-Mail', 'name' => 'E-Mail'])
        ->assertInertiaPath('data.meta.statuses.0', ['id' => 'Neu', 'name' => 'Neu'])
        ->assertInertiaPath('data.meta.members.0', ['id' => $member->id, 'name' => 'Aaaa Aaab'])
        ->assertInertiaPath('data.meta.subscriptions.0', ['name' => 'Beitrag', 'id' => $subscription->id])
        ->assertInertiaPath('data.meta.filter.statuses', ['Neu', 'Rechnung gestellt'])
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
            'usage' => '',
            'mail_email' => '',
        ])
        ->assertInertiaPath('data.meta.default_position', [
            'id' => null,
            'price' => 0,
            'description' => '',
            'member_id' => null,
        ]);
});

it('testValuesCanBeNull', function () {
    test()->login()->loginNami()->withoutExceptionHandling();
    Invoice::factory()->create();

    sleep(2);
    test()->get(route('invoice.index'))
        ->assertInertiaPath('data.data.0.sent_at_human', '');
});

it('filters for invoice status', function (array $filter, int $count) {
    test()->login()->loginNami()->withoutExceptionHandling();
    Invoice::factory()->status(InvoiceStatus::NEW)->create();
    Invoice::factory()->status(InvoiceStatus::SENT)->count(2)->create();
    Invoice::factory()->status(InvoiceStatus::PAID)->count(3)->create();

    sleep(2);
    test()->callFilter('invoice.index', $filter)->assertInertiaCount('data.data', $count);
})->with([
    [[], 3],
    [['statuses' => []], 0],
    [['statuses' => ['Neu']], 1],
    [['statuses' => ['Neu', 'Rechnung beglichen']], 4],
    [['statuses' => ['Neu', 'Rechnung beglichen', 'Rechnung gestellt']], 6],
]);

it('searches invoice usage', function (array $filter, int $count) {
    test()->login()->loginNami()->withoutExceptionHandling();
    Invoice::factory()->status(InvoiceStatus::NEW)->create(['usage' => 'Kein Zweck']);
    Invoice::factory()->status(InvoiceStatus::NEW)->create(['usage' => 'Mitgliedsbeitrag']);

    sleep(2);
    test()->callFilter('invoice.index', $filter)->assertInertiaCount('data.data', $count);
})->with([
    [['search' => 'Mitgliedsbeitrag'], 1],
    [['search' => 'Kein'], 1],
]);
