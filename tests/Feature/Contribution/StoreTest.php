<?php

namespace Tests\Feature\Contribution;

use App\Contribution\Documents\CitySolingenDocument;
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

it('compiles documents via base64 param', function (string $type, array $bodyChecks) {
    $this->withoutExceptionHandling();
    Tex::spy();
    $this->login()->loginNami();
    $member1 = Member::factory()->defaults()->male()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Max', 'lastname' => 'Muster']);
    $member2 = Member::factory()->defaults()->female()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Jane', 'lastname' => 'Muster']);

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
})->with('contribution-assertions');

it('testItCompilesGroupNameInSolingenDocument', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    Tex::spy();
    InvoiceSettings::fake(['from_long' => 'Stamm BiPi']);

    $this->call('GET', '/contribution-generate', [
        'payload' => ContributionRequestFactory::new()->type(CitySolingenDocument::class)->toBase64(),
    ]);

    Tex::assertCompiled(CitySolingenDocument::class, fn ($document) => $document->hasAllContent(['Stamm BiPi']));
});

it('testItCompilesContributionDocumentsViaApi', function (string $type, array $bodyChecks) {
    $this->withoutExceptionHandling();
    Tex::spy();
    Gender::factory()->female()->create();
    Gender::factory()->male()->create();
    Passport::actingAsClient(Client::factory()->create(), ['contribution-generate']);

    $response = $this->postJson('/api/contribution-generate', [
        'country' => Country::factory()->create()->id,
        'dateFrom' => '1991-06-15',
        'dateUntil' => '1991-06-16',
        'eventName' => 'Super tolles Lager',
        'type' => $type,
        'zipLocation' => '42777 SG',
        'member_data' => [
            ContributionMemberApiRequestFactory::new()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Max', 'lastname' => 'Muster']),
            ContributionMemberApiRequestFactory::new()->create(['address' => 'Maxstr 44', 'zip' => '42719', 'firstname' => 'Jane', 'lastname' => 'Muster']),
        ],
    ]);

    $response->assertSessionDoesntHaveErrors();
    $response->assertOk();
    Tex::assertCompiled($type, fn ($document) => $document->hasAllContent($bodyChecks));
})->with('contribution-assertions');

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
})->with('contribution-validation');

it('testItValidatesInputBeforeGeneration', function (array $input, string $documentClass, string $errorField) {
    $this->login()->loginNami();
    Country::factory()->create();
    Member::factory()->defaults()->create();

    $this->call('GET', '/contribution-generate', [
        'payload' => ContributionRequestFactory::new()->type($documentClass)->state($input)->toBase64(),
    ])->assertSessionHasErrors($errorField);
})->with('contribution-validation');
