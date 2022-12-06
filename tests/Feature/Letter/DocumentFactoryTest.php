<?php

namespace Tests\Feature\Letter;

use App\Letter\BillDocument;
use App\Letter\DocumentFactory;
use App\Letter\LetterSettings;
use App\Letter\Queries\LetterMemberQuery;
use App\Letter\Queries\SingleMemberQuery;
use App\Letter\RememberDocument;
use App\Member\Member;
use App\Payment\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class DocumentFactoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @testWith ["\\App\\Letter\\BillDocument"]
     *           ["\\App\\Letter\\RememberDocument"]
     */
    public function testItDoesntReturnARepositoryWhenMemberDoesntHavePayments(): void
    {
        $member = Member::factory()->defaults()->create();
        $letter = app(DocumentFactory::class)->singleLetter(BillDocument::class, $this->query($member));
        $this->assertNull($letter);
    }

    public function testItDisplaysMemberInformation(): void
    {
        $member = Member::factory()
            ->defaults()
            ->state([
                'firstname' => '::firstname::',
                'lastname' => '::lastname::',
                'address' => '::street::',
                'zip' => '::zip::',
                'location' => '::location::',
            ])
            ->has(Payment::factory()->notPaid()->nr('1995')->subscription('::subName::', 1500))
            ->create();

        $letter = app(DocumentFactory::class)->singleLetter(BillDocument::class, $this->query($member));

        $letter->assertHasAllContent([
            'Rechnung',
            '15.00',
            'Beitrag 1995 für ::firstname:: ::lastname:: (::subName::)',
            'Mitgliedsbeitrag für ::lastname::',
            'Familie ::lastname::\\\\::street::\\\\::zip:: ::location::',
        ]);
    }

    public function testBillSetsFilename(): void
    {
        $member = Member::factory()
            ->defaults()
            ->state(['lastname' => '::lastname::'])
            ->has(Payment::factory()->notPaid()->nr('1995')->subscription('::subName::', 1500))
            ->create();

        $letter = app(DocumentFactory::class)->singleLetter(BillDocument::class, $this->query($member));

        $this->assertEquals('rechnung-fur-lastname.pdf', $letter->compiledFilename());
    }

    public function testRememberSetsFilename(): void
    {
        $member = Member::factory()
            ->defaults()
            ->state(['lastname' => '::lastname::'])
            ->has(Payment::factory()->notPaid())
            ->create();

        $letter = app(DocumentFactory::class)->singleLetter(RememberDocument::class, $this->query($member));

        $this->assertEquals('zahlungserinnerung-fur-lastname.pdf', $letter->compiledFilename());
    }

    public function testItCreatesOneFileForFamilyMembers(): void
    {
        $firstMember = Member::factory()
            ->defaults()
            ->state(['firstname' => 'Max1', 'lastname' => '::lastname::', 'address' => '::address::', 'zip' => '12345', 'location' => '::location::'])
            ->has(Payment::factory()->notPaid()->nr('nr1'))
            ->create();
        Member::factory()
            ->defaults()
            ->state(['firstname' => 'Max2', 'lastname' => '::lastname::', 'address' => '::address::', 'zip' => '12345', 'location' => '::location::'])
            ->has(Payment::factory()->notPaid()->nr('nr2'))
            ->create();

        $letter = app(DocumentFactory::class)->singleLetter(BillDocument::class, $this->query($firstMember));

        $letter->assertHasAllContent(['Max1', 'Max2', 'nr1', 'nr2']);
    }

    /**
     * @testWith ["App\\Letter\\BillDocument"]
     *           ["App\\Letter\\RememberDocument"]
     */
    public function testItDisplaysSettings(string $type): void
    {
        LetterSettings::fake([
            'from_long' => 'langer Stammesname',
            'from' => 'Stammeskurz',
            'mobile' => '+49 176 55555',
            'email' => 'max@muster.de',
            'website' => 'https://example.com',
            'address' => 'Musterstr 4',
            'place' => 'Münster',
            'zip' => '12345',
            'iban' => 'DE444',
            'bic' => 'SOLSSSSS',
        ]);
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid()->nr('nr2'))
            ->create();

        $letter = app(DocumentFactory::class)->singleLetter($type, $this->query($member));

        $letter->assertHasAllContent([
            'langer Stammesname',
            'Stammeskurz',
            '+49 176 55555',
            'max@muster.de',
            'https://example.com',
            'Musterstr 4',
            'Münster',
            '12345',
            'DE444',
            'SOLSSSSS',
        ]);
    }

    public function testItGeneratesAPdf(): void
    {
        Tex::fake();
        $member = Member::factory()
            ->defaults()
            ->has(Payment::factory()->notPaid())
            ->create(['lastname' => 'lastname']);
        $this->withoutExceptionHandling();
        $this->login()->init()->loginNami();

        $response = $this->call('GET', "/member/{$member->id}/pdf", [
            'type' => BillDocument::class,
        ]);

        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertEquals('inline; filename="rechnung-fur-lastname.pdf"', $response->headers->get('content-disposition'));
    }

    private function query(Member $member): LetterMemberQuery
    {
        return new SingleMemberQuery($member);
    }
}
