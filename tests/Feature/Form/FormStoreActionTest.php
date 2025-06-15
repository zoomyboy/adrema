<?php

namespace Tests\Feature\Form;

use App\Fileshare\Data\FileshareResourceData;
use App\Form\Data\ExportData;
use App\Form\Enums\NamiType;
use App\Form\Models\Form;
use App\Lib\Events\Succeeded;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\RequestFactories\EditorRequestFactory;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function() {
    test()->setUpForm();
});

it('testItStoresForm', function () {
    Event::fake([Succeeded::class]);
    $this->login()->loginNami()->withoutExceptionHandling();
    $description = EditorRequestFactory::new()->text(10, 'Lorem');
    FormRequest::new()
        ->name('formname')
        ->description($description)
        ->excerpt('avff')
        ->preventionText(EditorRequestFactory::new()->paragraphs(['lorem ipsum']))
        ->registrationFrom('2023-05-04 01:00:00')->registrationUntil('2023-07-07 01:00:00')->from('2023-07-07')->to('2023-07-08')
        ->mailTop(EditorRequestFactory::new()->text(11, 'lala'))
        ->mailBottom(EditorRequestFactory::new()->text(12, 'lalab'))
        ->headerImage('htzz.jpg')
        ->sections([FormtemplateSectionRequest::new()->name('sname')->fields([$this->textField()->namiType(NamiType::BIRTHDAY)->forMembers(false)->hint('hhh')])])
        ->fake();

    $this->postJson(route('form.store'))->assertOk();

    $form = Form::latest()->first();
    $this->assertEquals('sname', $form->config->sections->get(0)->name);
    $this->assertEquals('formname', $form->name);
    $this->assertEquals('lorem ipsum', $form->prevention_text->blocks[0]['data']['text']);
    $this->assertEquals('avff', $form->excerpt);
    $this->assertEquals('Lorem', $form->description->blocks[0]['data']['text']);
    $this->assertEquals('lala', $form->mail_top->blocks[0]['data']['text']);
    $this->assertEquals('lalab', $form->mail_bottom->blocks[0]['data']['text']);
    $this->assertEquals('2023-05-04 01:00', $form->registration_from->format('Y-m-d H:i'));
    $this->assertEquals(true, $form->is_active);
    $this->assertEquals('2023-07-07 01:00', $form->registration_until->format('Y-m-d H:i'));
    $this->assertEquals('2023-07-07', $form->from->format('Y-m-d'));
    $this->assertEquals('2023-07-08', $form->to->format('Y-m-d'));
    $this->assertEquals('Geburtstag', $form->config->sections->get(0)->fields->get(0)->namiType->value);
    $this->assertEquals('hhh', $form->config->sections->get(0)->fields->get(0)->hint);
    $this->assertFalse($form->config->sections->get(0)->fields->get(0)->forMembers);
    $this->assertCount(1, $form->getMedia('headerImage'));
    $this->assertEquals('formname.jpg', $form->getMedia('headerImage')->first()->file_name);
    Event::assertDispatched(Succeeded::class, fn(Succeeded $event) => $event->message === 'Veranstaltung gespeichert.');
    $this->assertFrontendCacheCleared();
});

it('testItStoresDefaultSorting', function () {
    Event::fake([Succeeded::class]);
    $this->login()->loginNami()->withoutExceptionHandling();
    FormRequest::new()->fields([$this->textField()])->fake();

    $this->postJson(route('form.store'))->assertOk();

    $form = Form::latest()->first();
    $this->assertEquals('id', $form->meta['sorting']['by']);
    $this->assertFalse(false, $form->meta['sorting']['direction']);
});

it('testRegistrationDatesCanBeNull', function () {
    $this->login()->loginNami()->withoutExceptionHandling();

    $this->postJson(route('form.store'), FormRequest::new()->registrationFrom(null)->registrationUntil(null)->create())->assertOk();

    $this->assertDatabaseHas('forms', [
        'registration_until' => null,
        'registration_from' => null,
    ]);
});

it('testItStoresExport', function () {
    $this->login()->loginNami()->withoutExceptionHandling();

    $this->postJson(route('form.store'), FormRequest::new()->export(ExportData::from(['root' => FileshareResourceData::from(['connection_id' => 2, 'resource' => '/dir']), 'group_by' => 'lala', 'to_group_field' => 'abc']))->create())->assertOk();

    $form = Form::first();
    $this->assertEquals(2, $form->export->root->connectionId);
});


/**
 * @param array<string, string> $messages
 */
it('testItValidatesRequests', function (FormRequest $request, array $messages) {
    $this->login()->loginNami();
    $request->fake();

    $this->postJson(route('form.store'))->assertJsonValidationErrors($messages);
})->with([
    [FormRequest::new()->name(''), ['name' => 'Name ist erforderlich.']],
    [FormRequest::new()->excerpt(''), ['excerpt' => 'Auszug ist erforderlich.']],
    [FormRequest::new()->description(null), ['description.blocks' => 'Beschreibung ist erforderlich.']],
    [FormRequest::new()->state(['from' => null]), ['from' => 'Start ist erforderlich']],
    [FormRequest::new()->state(['to' => null]), ['to' => 'Ende ist erforderlich']],
    [FormRequest::new()->state(['header_image' => null]), ['header_image' => 'Bild ist erforderlich']],

]);
