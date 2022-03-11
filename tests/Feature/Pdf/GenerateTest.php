<?php

namespace Tests\Feature\Pdf;

use App\Country;
use App\Fee;
use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use App\Pdf\BillType;
use App\Pdf\PdfGenerator;
use App\Pdf\PdfRepositoryFactory;
use Carbon\Carbon;
use Database\Factories\Member\MemberFactory;
use Database\Factories\Payment\PaymentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Storage;
use Tests\TestCase;

class GenerateTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2021-04-15 00:00:00'));

        Storage::fake('temp');
    }

    public function generatorProvider(): array
    {
        return [
            'no_pdf_when_no_bill' => [
                'members' => [
                    [
                        'factory' => fn (MemberFactory $member): MemberFactory => $member,
                        'payments' => [],
                    ],
                ],
                'urlCallable' => fn (Collection $members): int => $members->first()->id,
                'type' => BillType::class,
                'filename' => null,
            ],
            'bill_for_single_member_when_no_bill_received_yet' => [
                'members' => [
                    [
                        'factory' => fn (MemberFactory $member): MemberFactory => $member
                            ->state([
                                'firstname' => '::firstname::',
                                'lastname' => '::lastname::',
                                'address' => '::street::',
                                'zip' => '::zip::',
                                'location' => '::location::',
                            ]),
                        'payments' => [
                            fn (PaymentFactory $payment): PaymentFactory => $payment
                                ->notPaid()
                                ->nr('1995')
                                ->subscription('::subName::', 1500),
                        ],
                    ],
                ],
                'urlCallable' => fn (Collection $members): int => $members->first()->id,
                'type' => BillType::class,
                'filename' => 'rechnung-fur-lastname.pdf',
                'output' => [
                    'Rechnung',
                    '15.00',
                    'Beitrag 1995 für ::firstname:: ::lastname:: (::subName::)',
                    'Mitgliedsbeitrag für ::lastname::',
                    'Familie ::lastname::\\\\::street::\\\\::zip:: ::location::',
                ],
            ],
            'bill_has_deadline' => [
                'members' => [
                    [
                        'factory' => fn (MemberFactory $member): MemberFactory => $member
                            ->state([
                                'firstname' => '::firstname::',
                                'lastname' => '::lastname::',
                            ]),
                        'payments' => [
                            fn (PaymentFactory $payment): PaymentFactory => $payment
                                ->nr('A')->notPaid()->subscription('::subName::', 1500),
                        ],
                    ],
                ],
                'urlCallable' => fn (Collection $members): int => $members->first()->id,
                'type' => BillType::class,
                'filename' => 'rechnung-fur-lastname.pdf',
                'output' => [
                    '29.04.2021',
                ],
            ],
            'families' => [
                'members' => [
                    [
                        'factory' => fn (MemberFactory $member): MemberFactory => $member
                            ->state([
                                'firstname' => '::firstname1::',
                                'lastname' => '::lastname::',
                                'address' => '::address::',
                                'zip' => '12345',
                                'location' => '::location::',
                            ]),
                        'payments' => [
                            fn (PaymentFactory $payment): PaymentFactory => $payment
                                ->nr('::nr::')->notPaid()->subscription('::subName::', 1500),
                        ],
                    ],
                    [
                        'factory' => fn (MemberFactory $member): MemberFactory => $member
                            ->state([
                                'firstname' => '::firstname2::',
                                'lastname' => '::lastname::',
                                'address' => '::address::',
                                'zip' => '12345',
                                'location' => '::location::',
                            ]),
                        'payments' => [
                            fn (PaymentFactory $payment): PaymentFactory => $payment
                                ->nr('::nr2::')->notPaid()->subscription('::subName2::', 1600),
                        ],
                    ],
                ],
                'urlCallable' => fn (Collection $members): int => $members->first()->id,
                'type' => BillType::class,
                'filename' => 'rechnung-fur-lastname.pdf',
                'output' => [
                    '::nr::',
                    '::nr2::',
                    '::subName::',
                    '::subName2::',
                ],
            ],
        ];
    }

    /** @dataProvider generatorProvider */
    public function testItGeneratesTheLayout(
        array $members,
        callable $urlCallable,
        string $type,
        ?string $filename = null,
        ?array $output = null
    ): void {
        $this->withoutExceptionHandling();
        $this->login()->init();
        $members = $this->setupMembers($members);

        $urlId = call_user_func($urlCallable, $members);
        $member = Member::find($urlId);
        $repo = app(PdfRepositoryFactory::class)->fromSingleRequest($type, $member);

        if (null === $filename) {
            $this->assertNull($repo);

            return;
        }

        $content = app(PdfGenerator::class)->setRepository($repo)->compileView();

        foreach ($output as $out) {
            $this->assertStringContainsString($out, $content);
        }
    }

    /** @dataProvider generatorProvider */
    public function testItGeneratesAPdf(
        array $members,
        callable $urlCallable,
        string $type,
        ?string $filename = null,
        ?array $output = null
    ): void {
        $this->withoutExceptionHandling();
        $this->login()->init();
        $members = $this->setupMembers($members);

        $urlId = call_user_func($urlCallable, $members);
        $response = $this->call('GET', "/member/{$urlId}/pdf", [
            'type' => $type,
        ]);

        if (null === $filename) {
            $response->assertStatus(204);

            return;
        }

        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertEquals('inline; filename="'.$filename.'"', $response->headers->get('content-disposition'));
    }

    private function setupMembers(array $members): Collection
    {
        return collect($members)->map(function (array $member): Member {
            $memberFactory = Member::factory()
                ->for(Nationality::factory())
                ->for(Subscription::factory()->for(Fee::factory()))
                ->withPayments(data_get($member, 'payments', []))
                ->forCountry(Country::find(5))
                ->for(Group::factory());
            $memberModel = call_user_func($member['factory'], $memberFactory)->create();

            return $memberModel->load('payments');
        });
    }
}
