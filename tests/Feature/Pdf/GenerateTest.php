<?php

namespace Tests\Feature\Pdf;

use App\Country;
use App\Fee;
use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Payment\Payment;
use App\Payment\Subscription;
use App\Pdf\BillType;
use App\Pdf\PdfGenerator;
use App\Pdf\PdfRepositoryFactory;
use Database\Factories\Member\MemberFactory;
use Database\Factories\Payment\PaymentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Storage;
use Tests\TestCase;
use Tests\Traits\FakesTex;

class GenerateTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

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
                            ->state(['firstname' => '::firstname::', 'lastname' => '::lastname::']),
                        'payments' => [
                            fn (PaymentFactory $payment): PaymentFactory => $payment
                                ->notPaid()
                                ->nr('1995')
                                ->subscription('::subName::', 1200),
                        ],
                    ],
                ],
                'urlCallable' => fn (Collection $members): int => $members->first()->id,
                'type' => BillType::class,
                'filename' => 'rechnung-fur-firstname-lastname.pdf',
                'output' => [
                    '12.00',
                    'Familie ::lastname::',
                ],
            ],
        ];
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
        $this->login();
        $members = $this->setupMembers($members);

        $urlId = call_user_func($urlCallable, $members);
        $response = $this->call('GET', "/member/{$urlId}/pdf", [
            'type' => $type,
        ]);

        if ($filename === null) {
            $response->assertStatus(204);

            return;
        }

        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertEquals('inline; filename="' . $filename . '"', $response->headers->get('content-disposition'));
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
        $this->login();
        $members = $this->setupMembers($members);

        $urlId = call_user_func($urlCallable, $members);
        $member = Member::find($urlId);
        $repo = app(PdfRepositoryFactory::class)->fromSingleRequest($type, $member);

        if ($filename === null) {
            $this->assertNull($repo);

            return;
        }

        $content = app(PdfGenerator::class)->setRepository($repo)->compileView();

        foreach ($output as $out) {
            $this->assertStringContainsString($out, $content);
        }
    }

    private function setupMembers(array $members): Collection
    {
        return collect($members)->map(function (array $member): Member {
            $memberFactory = Member::factory()
                ->for(Nationality::factory())
                ->for(Subscription::factory()->for(Fee::factory()))
                ->forCountry(Country::find(5))
                ->for(Group::factory());
            $memberModel = call_user_func($member['factory'], $memberFactory)->create();

            foreach (data_get($member, 'payments', []) as $payment) {
                $paymentFactory = Payment::factory()->for($memberModel);
                $paymentFactory = call_user_func($payment, $paymentFactory);
                $paymentFactory->create();
            }

            return $memberModel->load('payments');
        });
    }

}
