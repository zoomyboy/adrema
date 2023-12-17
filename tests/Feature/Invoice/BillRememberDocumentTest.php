<?php

namespace Tests\Feature\Invoice;

use App\Invoice\BillDocument;
use App\Invoice\BillKind;
use App\Invoice\Invoice;
use App\Invoice\Queries\BillKindQuery;
use App\Invoice\Queries\InvoiceMemberQuery;
use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BillRememberDocumentTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCreatesOneFileForFamilyMembers(): void
    {
        Member::factory()
            ->defaults()
            ->postBillKind()
            ->state(['firstname' => 'Max1', 'lastname' => '::lastname::', 'address' => '::address::', 'zip' => '12345', 'location' => '::location::'])
            ->has(Payment::factory()->notPaid()->nr('nr1'))
            ->create();
        Member::factory()
            ->defaults()
            ->postBillKind()
            ->state(['firstname' => 'Max2', 'lastname' => '::lastname::', 'address' => '::address::', 'zip' => '12345', 'location' => '::location::'])
            ->has(Payment::factory()->notPaid()->nr('nr2'))
            ->create();

        $this->assertCount(2, $this->query(BillDocument::class)->getMembers()->first());
    }

    /**
     * @param class-string<Invoice> $type
     */
    private function query(string $type): InvoiceMemberQuery
    {
        return (new BillKindQuery(BillKind::POST))->type($type);
    }
}
