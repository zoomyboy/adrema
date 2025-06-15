<?php

namespace Tests\Feature\Form;

use App\Form\Enums\SpecialType;
use App\Form\Fields\TextareaField;
use App\Form\Fields\TextField;
use App\Form\Models\Formtemplate;
use App\Lib\Events\Succeeded;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\Lib\CreatesFormFields;
use Tests\RequestFactories\EditorRequestFactory;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

it('testItStoresTemplates', function () {
    Event::fake([Succeeded::class]);
    $this->login()->loginNami()->withoutExceptionHandling();
    $payload = FormtemplateRequest::new()->name('testname')->sections([
        FormtemplateSectionRequest::new()->name('Persönliches')->fields([
            $this->textField('a')->name('lala1')->columns(['mobile' => 2, 'tablet' => 2, 'desktop' => 1])->required(false)->hint('hhh')->intro('intro'),
            $this->textareaField('b')->name('lala2')->required(false)->specialType(SpecialType::FIRSTNAME)->rows(10),
        ]),
    ])
        ->mailTop(EditorRequestFactory::new()->text(10, 'lala'))
        ->mailBottom(EditorRequestFactory::new()->text(10, 'lblb'))
        ->create();

    $this->postJson(route('formtemplate.store'), $payload)->assertOk();

    $formtemplate = Formtemplate::latest()->first();
    $this->assertEquals('Persönliches', $formtemplate->config->sections->get(0)->name);
    $this->assertEquals('lala1', $formtemplate->config->sections->get(0)->fields->get(0)->name);
    $this->assertNull($formtemplate->config->sections->get(0)->fields->get(0)->specialType);
    $this->assertEquals('hhh', $formtemplate->config->sections->get(0)->fields->get(0)->hint);
    $this->assertEquals('intro', $formtemplate->config->sections->get(0)->fields->get(0)->intro);
    $this->assertInstanceOf(TextField::class, $formtemplate->config->sections->get(0)->fields->get(0));
    $this->assertInstanceOf(TextareaField::class, $formtemplate->config->sections->get(0)->fields->get(1));
    $this->assertEquals(false, $formtemplate->config->sections->get(0)->fields->get(1)->required);
    $this->assertEquals(SpecialType::FIRSTNAME, $formtemplate->config->sections->get(0)->fields->get(1)->specialType);
    $this->assertEquals(['mobile' => 2, 'tablet' => 2, 'desktop' => 1], $formtemplate->config->sections->get(0)->fields->get(0)->columns->toArray());
    $this->assertEquals(10, $formtemplate->config->sections->get(0)->fields->get(1)->rows);
    $this->assertEquals('lala', $formtemplate->mail_top->blocks[0]['data']['text']);
    $this->assertEquals('lblb', $formtemplate->mail_bottom->blocks[0]['data']['text']);
    $this->assertFalse($formtemplate->config->sections->get(0)->fields->get(0)->required);
    Event::assertDispatched(Succeeded::class, fn(Succeeded $event) => $event->message === 'Vorlage gespeichert.');
});

/**
 * @param array<string, string> $messages
 */
it('testItValidatesRequests', function (FormtemplateRequest $request, array $messages) {
    $this->login()->loginNami();
    $request->fake();

    $this->postJson(route('formtemplate.store'))
        ->assertJsonValidationErrors($messages);
})->with([
    fn () => [FormtemplateRequest::new()->name(''), ['name' => 'Name ist erforderlich.']],
    fn () => [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->name('')]), ['config.sections.0.name' => 'Sektionsname ist erforderlich.']],
    fn () => [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
        test()->textField()->name(''),
    ])]), ['config.sections.0.fields.0.name' => 'Feldname ist erforderlich.']],
    fn () => [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
        FormtemplateFieldRequest::type('')
    ])]), ['config.sections.0.fields.0.type' => 'Feldtyp ist erforderlich.']],
    fn () => [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
        FormtemplateFieldRequest::type('aaaaa'),
    ])]), ['config.sections.0.fields.0.type' => 'Feldtyp ist ungültig.']],
    fn () => [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
        test()->textField(''),
    ])]), ['config.sections.0.fields.0.key' => 'Feldkey ist erforderlich.']],
    fn () => [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
        test()->textField('a b'),
    ])]), ['config.sections.0.fields.0.key' => 'Feldkey Format ist ungültig.']],
    fn () => [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
        test()->textField()->required('la')
    ])]), ['config.sections.0.fields.0.required' => 'Erforderlich muss ein Wahrheitswert sein.']],
]);
