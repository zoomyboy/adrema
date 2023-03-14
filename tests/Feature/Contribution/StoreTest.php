<?php

namespace Tests\Feature\Contribution;

use App\Contribution\Documents\ContributionDocument;
use App\Contribution\Documents\DvDocument;
use App\Contribution\Documents\SolingenDocument;
use App\Country;
use App\Member\Member;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\ContributionRequestFactory;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @testWith ["App\\Contribution\\Documents\\SolingenDocument", ["Super tolles Lager", "Max Muster", "Jane Muster", "15.06.1991"]]
     *  ["App\\Contribution\\Documents\\DvDocument", ["Muster, Max", "Muster, Jane", "15.06.1991", "42777 SG"]]
     *  ["App\\Contribution\\Documents\\RemscheidDocument", ["Max", "Muster", "Jane"]]
     *
     * @param array<int, string> $bodyChecks
     */
    public function testItCompilesContributionDocuments(string $type, array $bodyChecks): void
    {
        $this->withoutExceptionHandling();
        Tex::spy();
        $this->login()->loginNami();
        $country = Country::factory()->create();
        $member1 = Member::factory()->defaults()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Max', 'lastname' => 'Muster']);
        $member2 = Member::factory()->defaults()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Jane', 'lastname' => 'Muster']);

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
            SolingenDocument::class,
            'type',
        ];
        yield [
            ['type' => ''],
            SolingenDocument::class,
            'type',
        ];
        yield [
            ['dateFrom' => ''],
            SolingenDocument::class,
            'dateFrom',
        ];
        yield [
            ['dateFrom' => '2022-01'],
            SolingenDocument::class,
            'dateFrom',
        ];
        yield [
            ['dateUntil' => ''],
            SolingenDocument::class,
            'dateUntil',
        ];
        yield [
            ['dateUntil' => '2022-01'],
            SolingenDocument::class,
            'dateUntil',
        ];
        yield [
            ['country' => -1],
            DvDocument::class,
            'country',
        ];
        yield [
            ['country' => 'AAAA'],
            DvDocument::class,
            'country',
        ];
        yield [
            ['members' => 'A'],
            DvDocument::class,
            'members',
        ];
        yield [
            ['members' => [99999]],
            DvDocument::class,
            'members.0',
        ];
        yield [
            ['members' => ['lalala']],
            DvDocument::class,
            'members.0',
        ];
        yield [
            ['eventName' => ''],
            SolingenDocument::class,
            'eventName',
        ];
        yield [
            ['zipLocation' => ''],
            SolingenDocument::class,
            'zipLocation',
        ];
    }
}
