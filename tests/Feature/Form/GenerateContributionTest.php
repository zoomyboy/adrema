<?php

namespace Tests\Feature\Form;

use App\Contribution\Documents\CitySolingenDocument;
use App\Contribution\Documents\RdpNrwDocument;
use App\Country;
use App\Form\Enums\SpecialType;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Requests\FormCompileRequest;
use App\Gender;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\CreatesFormFields;
use Tests\RequestFactories\ConditionRequestFactory;
use Zoomyboy\Tex\Tex;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

mutates(FormCompileRequest::class);

beforeEach(function() {
    Country::factory()->create();
    Gender::factory()->male()->create();
    Gender::factory()->female()->create();
    Tex::spy();
    $this->login()->loginNami();
});

it('doesnt create document when no special fields given', function (array $fields, string $field, string $message, string $type) {
    $form = Form::factory()
        ->fields($fields)
        ->has(Participant::factory())
        ->create();

    generate($type, $form, true)->assertJsonValidationErrors([$field => $message]);
})
    ->with([
        [fn() => [], 'FIRSTNAME', 'Kein Feld für Vorname vorhanden.'],
        [fn() => [test()->textField('f')->specialType(SpecialType::FIRSTNAME)], 'LASTNAME', 'Kein Feld für Nachname vorhanden.'],
        [fn() => [test()->textField('f')->specialType(SpecialType::FIRSTNAME), test()->textField('l')->specialType(SpecialType::LASTNAME)], 'BIRTHDAY', 'Kein Feld für Geburtsdatum vorhanden.'],
        [fn() => [test()->textField('f')->specialType(SpecialType::FIRSTNAME), test()->textField('l')->specialType(SpecialType::LASTNAME), test()->dateField('b')->specialType(SpecialType::BIRTHDAY)], 'ZIP', 'Kein Feld für PLZ vorhanden.'],
        [fn() => [test()->textField('f')->specialType(SpecialType::FIRSTNAME), test()->textField('l')->specialType(SpecialType::LASTNAME), test()->dateField('b')->specialType(SpecialType::BIRTHDAY), test()->dateField('p')->specialType(SpecialType::ZIP)], 'LOCATION', 'Kein Feld für Ort vorhanden.'],
    ])->with('contribution-documents');

it('validates special types of each document', function (string $type, array $fields, string $field,  string $message) {
    $form = Form::factory()->fields([
        test()->textField('f')->specialType(SpecialType::FIRSTNAME),
        test()->textField('l')->specialType(SpecialType::LASTNAME),
        test()->dateField('b')->specialType(SpecialType::BIRTHDAY),
        test()->dateField('p')->specialType(SpecialType::ZIP),
        test()->dateField('l')->specialType(SpecialType::LOCATION),
        ...$fields,
    ])
        ->has(Participant::factory())
        ->create();

    generate($type, $form, true)->assertJsonValidationErrors([$field => $message]);
})
    ->with([
        [CitySolingenDocument::class, [], 'ADDRESS', 'Kein Feld für Adresse vorhanden.'],
        [RdpNrwDocument::class, [], 'GENDER', 'Kein Feld für Geschlecht vorhanden.'],
    ]);

it('throws error when not validating but fields are not present', function () {
    $form = Form::factory()->fields([])
        ->has(Participant::factory())
        ->create();

    generate(CitySolingenDocument::class, $form, false)->assertStatus(422);
});

it('throws error when form doesnt have meta', function () {
    $form = Form::factory()->fields([])
        ->has(Participant::factory())
        ->zip('')
        ->location('')
        ->create();

    generate(CitySolingenDocument::class, $form, false)->assertStatus(422)->assertJsonValidationErrors([
        'zip' => 'PLZ ist erforderlich.',
        'location' => 'Ort ist erforderlich.'
    ]);
});

it('throws error when form doesnt have participants', function () {
    $form = Form::factory()->fields([])->create();

    generate(CitySolingenDocument::class, $form, true)->assertJsonValidationErrors(['participants' => 'Veranstaltung besitzt noch keine Teilnehmer*innen.']);
});

dataset('default-form-contribution', fn () => [
    [
        ['fn' => 'Baum', 'ln' => 'Muster', 'bd' => '1991-05-06', 'zip' => '33333', 'loc' => 'Musterstadt', 'add' => 'Laastr 4', 'gen' => 'weiblich'],
        fn () => [
            test()->textField('fn')->specialType(SpecialType::FIRSTNAME),
            test()->textField('ln')->specialType(SpecialType::LASTNAME),
            test()->dateField('bd')->specialType(SpecialType::BIRTHDAY),
            test()->dateField('zip')->specialType(SpecialType::ZIP),
            test()->dateField('loc')->specialType(SpecialType::LOCATION),
            test()->dateField('add')->specialType(SpecialType::ADDRESS),
            test()->dateField('gen')->specialType(SpecialType::GENDER),
        ]
    ]
]);

dataset('form-contributions', fn () => [
    [
        [],
        [],
        CitySolingenDocument::class,
        ['Baum', 'Muster', '1991', 'Musterstadt', 'Laastr 4', '33333'],
    ],
    [
        ['gen' => 'männlich'],
        [],
        RdpNrwDocument::class,
        ['{m}'],
    ],
    [
        ['gen' => 'weiblich'],
        [],
        RdpNrwDocument::class,
        ['{w}'],
    ],
]);

it('creates document with participant data', function (array $defaultData, array $defaultFields, array $newData, array $newFields, string $document, array $expected) {
    $form = Form::factory()->fields([
        ...$defaultFields,
        ...$newFields,
    ])
        ->has(Participant::factory()->data([...$defaultData, ...$newData]))
        ->create();

    generate($document, $form, false)->assertOk();
    Tex::assertCompiled($document, fn($document) => $document->hasAllContent($expected));
})->with('default-form-contribution')->with('form-contributions');

it('creates document with is leader', function (array $defaultData, array $fields) {
    $form = Form::factory()->fields([
        ...$fields,
        test()->dropdownField('leader')->options(['L', 'NL'])->specialType(SpecialType::LEADER),
    ])
        ->has(Participant::factory()->data([...$defaultData, 'leader' => 'L']))
        ->leaderConditions(ConditionRequestFactory::new()->whenField('leader', 'L')->create())
        ->create();

    generate(RdpNrwDocument::class, $form, false)->assertOk();
    Tex::assertCompiled(RdpNrwDocument::class, fn($document) => $document->hasAllContent(['{L}']));
})->with('default-form-contribution');

it('creates document with form meta', function () {
    $form = Form::factory()->fields([
        test()->textField('fn')->specialType(SpecialType::FIRSTNAME),
        test()->textField('ln')->specialType(SpecialType::LASTNAME),
        test()->dateField('bd')->specialType(SpecialType::BIRTHDAY),
        test()->dateField('zip')->specialType(SpecialType::ZIP),
        test()->dateField('loc')->specialType(SpecialType::LOCATION),
        test()->dateField('add')->specialType(SpecialType::ADDRESS),
        test()->dateField('gen')->specialType(SpecialType::GENDER),
    ])
        ->has(Participant::factory()->data(['fn' => 'Baum', 'ln' => 'Muster', 'bd' => '1991-05-06', 'zip' => '33333', 'loc' => 'Musterstadt', 'add' => 'Laastr 4', 'gen' => 'weiblich']))
        ->name('Sommerlager')
        ->from('2008-06-20')
        ->to('2008-06-22')
        ->zip('12345')
        ->location('Frankfurt')
        ->create();

    generate(RdpNrwDocument::class, $form, false)->assertOk();
    Tex::assertCompiled(RdpNrwDocument::class, fn($document) => $document->hasAllContent(['20.06.2008', '22.06.2008', '12345 Frankfurt']));
});

function generate(string $document, Form $form, bool $validate) {
    return test()->json('GET', route('form.contribution', [
        'payload' => test()->filterString(['type' => $document]),
        'form' => $form,
        'validate' => $validate ? '1' : '0'
    ]));
}
