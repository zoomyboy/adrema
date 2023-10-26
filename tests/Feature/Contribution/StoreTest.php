<?php

namespace Tests\Feature\Contribution;

use App\Contribution\Documents\ContributionDocument;
use App\Contribution\Documents\RdpNrwDocument;
use App\Contribution\Documents\CitySolingenDocument;
use App\Country;
use App\Gender;
use App\Invoice\InvoiceSettings;
use App\Member\Member;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Tests\RequestFactories\ContributionMemberApiRequestFactory;
use Tests\RequestFactories\ContributionRequestFactory;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @testWith ["App\\Contribution\\Documents\\CitySolingenDocument", ["Super tolles Lager", "Max Muster", "Jane Muster", "15.06.1991"]]
     *  ["App\\Contribution\\Documents\\RdpNrwDocument", ["Muster, Max", "Muster, Jane", "15.06.1991", "42777 SG"]]
     *  ["App\\Contribution\\Documents\\CityRemscheidDocument", ["Max", "Muster", "Jane"]]
     *  ["App\\Contribution\\Documents\\CityFrankfurtMainDocument", ["Max", "Muster", "Jane"]]
     *  ["App\\Contribution\\Documents\\BdkjHesse", ["Max", "Muster", "Jane"]]
     *
     * @param array<int, string> $bodyChecks
     */
    public function testItCompilesContributionDocumentsViaRequest(string $type, array $bodyChecks): void
    {
        $this->withoutExceptionHandling();
        Tex::spy();
        $this->login()->loginNami();
        $country = Country::factory()->create();
        $member1 = Member::factory()->defaults()->for(Gender::factory())->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Max', 'lastname' => 'Muster']);
        $member2 = Member::factory()->defaults()->for(Gender::factory())->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Jane', 'lastname' => 'Muster']);

        $response = $this->call('GET', '/contribution-generate', [
            'payload' => base64_encode(json_encode([
                'country' => $country->id,
                'dateFrom' => '1991-06-15',
                'dateUntil' => '1991-06-16',
                'eventName' => 'Super tolles Lager',
                'members' => [$member1->id, $member2->id],
                'type' => $type,
                'zipLocation' => '42777 SG',
            ])),
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertOk();
        Tex::assertCompiled($type, fn ($document) => $document->hasAllContent($bodyChecks));
    }

    public function testItCompilesGroupNameInSolingenDocument(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Tex::spy();
        $member = Member::factory()->defaults()->for(Gender::factory())->create();

        InvoiceSettings::fake([
            'from_long' => 'Stamm BiPi',
        ]);

        $this->call('GET', '/contribution-generate', [
            'payload' => base64_encode(json_encode([
                'country' => Country::factory()->create()->id,
                'dateFrom' => '1991-06-15',
                'dateUntil' => '1991-06-16',
                'eventName' => 'Super tolles Lager',
                'members' => [$member->id],
                'type' => CitySolingenDocument::class,
                'zipLocation' => '42777 SG',
            ])),
        ]);

        Tex::assertCompiled(CitySolingenDocument::class, fn ($document) => $document->hasAllContent(['Stamm BiPi']));
    }

    public function testItCompilesContributionDocumentsViaApi(): void
    {
        $this->withoutExceptionHandling();
        Tex::spy();
        Gender::factory()->create(['name' => 'Weiblich']);
        Gender::factory()->create(['name' => 'Männlich']);
        Passport::actingAsClient(Client::factory()->create(), ['contribution-generate']);
        $country = Country::factory()->create();
        Member::factory()->defaults()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Max', 'lastname' => 'Muster']);
        Member::factory()->defaults()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Jane', 'lastname' => 'Muster']);

        $response = $this->postJson('/api/contribution-generate', [
            'country' => $country->id,
            'dateFrom' => '1991-06-15',
            'dateUntil' => '1991-06-16',
            'eventName' => 'Super tolles Lager',
            'type' => CitySolingenDocument::class,
            'zipLocation' => '42777 SG',
            'member_data' => [
                ContributionMemberApiRequestFactory::new()->create(),
                ContributionMemberApiRequestFactory::new()->create(),
            ],
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertOk();
        Tex::assertCompiled(CitySolingenDocument::class, fn ($document) => $document->hasAllContent(['Super']));
    }

    /**
     * @testWith [""]
     *           ["aaaa"]
     *           ["YWFhCg=="]
     */
    public function testInputShouldBeBase64EncodedJson(string $payload): void
    {
        $this->login()->loginNami();

        $this->call('GET', '/contribution-generate', ['payload' => $payload])->assertSessionHasErrors('payload');
    }

    /**
     * @param array<string, string>              $input
     * @param class-string<ContributionDocument> $documentClass
     * @dataProvider validationDataProvider
     */
    public function testItValidatesInput(array $input, string $documentClass, string $errorField): void
    {
        $this->login()->loginNami();
        Country::factory()->create();
        Member::factory()->defaults()->create();

        $this->postJson('/contribution-validate', ContributionRequestFactory::new()->type($documentClass)->state($input)->create())
            ->assertJsonValidationErrors($errorField);
    }

    /**
     * @param array<string, string>              $input
     * @param class-string<ContributionDocument> $documentClass
     * @dataProvider validationDataProvider
     */
    public function testItValidatesInputBeforeGeneration(array $input, string $documentClass, string $errorField): void
    {
        $this->login()->loginNami();
        Country::factory()->create();
        Member::factory()->defaults()->create();

        $this->call('GET', '/contribution-generate', [
            'payload' => ContributionRequestFactory::new()->type($documentClass)->state($input)->toBase64(),
        ])->assertSessionHasErrors($errorField);
    }

    protected function validationDataProvider(): Generator
    {
        yield [
            ['type' => 'aaa'],
            CitySolingenDocument::class,
            'type',
        ];
        yield [
            ['type' => ''],
            CitySolingenDocument::class,
            'type',
        ];
        yield [
            ['dateFrom' => ''],
            CitySolingenDocument::class,
            'dateFrom',
        ];
        yield [
            ['dateFrom' => '2022-01'],
            CitySolingenDocument::class,
            'dateFrom',
        ];
        yield [
            ['dateUntil' => ''],
            CitySolingenDocument::class,
            'dateUntil',
        ];
        yield [
            ['dateUntil' => '2022-01'],
            CitySolingenDocument::class,
            'dateUntil',
        ];
        yield [
            ['country' => -1],
            RdpNrwDocument::class,
            'country',
        ];
        yield [
            ['country' => 'AAAA'],
            RdpNrwDocument::class,
            'country',
        ];
        yield [
            ['members' => 'A'],
            RdpNrwDocument::class,
            'members',
        ];
        yield [
            ['members' => [99999]],
            RdpNrwDocument::class,
            'members.0',
        ];
        yield [
            ['members' => ['lalala']],
            RdpNrwDocument::class,
            'members.0',
        ];
        yield [
            ['eventName' => ''],
            CitySolingenDocument::class,
            'eventName',
        ];
        yield [
            ['zipLocation' => ''],
            CitySolingenDocument::class,
            'zipLocation',
        ];
    }
}
