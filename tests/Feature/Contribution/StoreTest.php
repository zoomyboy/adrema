<?php

namespace Tests\Feature\Contribution;

use App\Contribution\Documents\RdpNrwDocument;
use App\Contribution\Documents\CitySolingenDocument;
use App\Contribution\Documents\GallierDocument;
use App\Country;
use App\Gender;
use App\Invoice\InvoiceSettings;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Tests\RequestFactories\ContributionMemberApiRequestFactory;
use Tests\RequestFactories\ContributionRequestFactory;
use Zoomyboy\Tex\Tex;

uses(DatabaseTransactions::class);

dataset('validation', function () {
    return [
        [
            ['type' => 'aaa'],
            CitySolingenDocument::class,
            'type',
        ],
        [
            ['type' => ''],
            CitySolingenDocument::class,
            'type',
        ],
        [
            ['dateFrom' => ''],
            CitySolingenDocument::class,
            'dateFrom',
        ],
        [
            ['dateFrom' => '2022-01'],
            CitySolingenDocument::class,
            'dateFrom',
        ],
        [
            ['dateUntil' => ''],
            CitySolingenDocument::class,
            'dateUntil',
        ],
        [
            ['dateUntil' => '2022-01'],
            CitySolingenDocument::class,
            'dateUntil',
        ],
        [
            ['country' => -1],
            RdpNrwDocument::class,
            'country',
        ],
        [
            ['country' => 'AAAA'],
            RdpNrwDocument::class,
            'country',
        ],
        [
            ['members' => 'A'],
            RdpNrwDocument::class,
            'members',
        ],
        [
            ['members' => [99999]],
            RdpNrwDocument::class,
            'members.0',
        ],
        [
            ['members' => ['lalala']],
            RdpNrwDocument::class,
            'members.0',
        ],
        [
            ['eventName' => ''],
            CitySolingenDocument::class,
            'eventName',
        ],
        [
            ['zipLocation' => ''],
            CitySolingenDocument::class,
            'zipLocation',
        ],
        [
            ['zipLocation' => ''],
            GallierDocument::class,
            'zipLocation',
        ],
        [
            ['dateFrom' => ''],
            GallierDocument::class,
            'dateFrom',
        ],
        [
            ['dateUntil' => ''],
            GallierDocument::class,
            'dateUntil',
        ],
    ];
});

it('compiles documents via api', function (string $type, array $bodyChecks) {
    $this->withoutExceptionHandling();
    Tex::spy();
    $this->login()->loginNami();
    $member1 = Member::factory()->defaults()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Max', 'lastname' => 'Muster']);
    $member2 = Member::factory()->defaults()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Jane', 'lastname' => 'Muster']);

    $response = $this->call('GET', '/contribution-generate', [
        'payload' => ContributionRequestFactory::new()->type($type)->state([
            'dateFrom' => '1991-06-15',
            'dateUntil' => '1991-06-16',
            'eventName' => 'Super tolles Lager',
            'members' => [$member1->id, $member2->id],
            'type' => $type,
            'zipLocation' => '42777 SG',
        ])->toBase64(),
    ]);

    $response->assertSessionDoesntHaveErrors();
    $response->assertOk();
    Tex::assertCompiled($type, fn ($document) => $document->hasAllContent($bodyChecks));
})->with([
    ["App\\Contribution\\Documents\\CitySolingenDocument", ["Super tolles Lager", "Max Muster", "Jane Muster", "15.06.1991"]],
    ["App\\Contribution\\Documents\\RdpNrwDocument", ["Muster, Max", "Muster, Jane", "15.06.1991", "42777 SG"]],
    ["App\\Contribution\\Documents\\CityRemscheidDocument", ["Max", "Muster", "Jane"]],
    ["App\\Contribution\\Documents\\CityFrankfurtMainDocument", ["Max", "Muster", "Jane"]],
    ["App\\Contribution\\Documents\\BdkjHesse", ["Max", "Muster", "Jane"]],
    ["App\\Contribution\\Documents\\GallierDocument", ["Max", "Muster", "Jane", "42777 SG", "15.06.1991", "16.06.1991"]],
]);

it('testItCompilesGroupNameInSolingenDocument', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    Tex::spy();
    InvoiceSettings::fake(['from_long' => 'Stamm BiPi']);

    $this->call('GET', '/contribution-generate', [
        'payload' => ContributionRequestFactory::new()->type(CitySolingenDocument::class)->toBase64(),
    ]);

    Tex::assertCompiled(CitySolingenDocument::class, fn ($document) => $document->hasAllContent(['Stamm BiPi']));
});

it('testItCompilesContributionDocumentsViaApi', function () {
    $this->withoutExceptionHandling();
    Tex::spy();
    Gender::factory()->female()->create();
    Gender::factory()->male()->create();
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
});

it('testInputShouldBeBase64EncodedJson', function (string $payload) {
    $this->login()->loginNami();

    $this->call('GET', '/contribution-generate', ['payload' => $payload])->assertSessionHasErrors('payload');
})->with([
    [""],
    ["aaaa"],
    ["YWFhCg=="],
]);

it('testItValidatesInput', function (array $input, string $documentClass, string $errorField) {
    $this->login()->loginNami();
    Country::factory()->create();
    Member::factory()->defaults()->create();

    $this->postJson('/contribution-validate', ContributionRequestFactory::new()->type($documentClass)->state($input)->create())
        ->assertJsonValidationErrors($errorField);
})->with('validation');

it('testItValidatesInputBeforeGeneration', function (array $input, string $documentClass, string $errorField) {
    $this->login()->loginNami();
    Country::factory()->create();
    Member::factory()->defaults()->create();

    $this->call('GET', '/contribution-generate', [
        'payload' => ContributionRequestFactory::new()->type($documentClass)->state($input)->toBase64(),
    ])->assertSessionHasErrors($errorField);
})->with('validation');
