<?php

namespace Tests\Feature\Form;

use App\Form\Fields\TextareaField;
use App\Form\Fields\TextField;
use App\Form\Models\Formtemplate;
use App\Lib\Events\Succeeded;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Generator;

class FormtemplateStoreActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItStoresTemplates(): void
    {
        Event::fake([Succeeded::class]);
        $this->login()->loginNami()->withoutExceptionHandling();
        FormtemplateRequest::new()->name('testname')->sections([
            FormtemplateSectionRequest::new()->name('Persönliches')->fields([
                FormtemplateFieldRequest::new()->type(TextField::class)->name('lala1')->columns(['mobile' => 2, 'tablet' => 2, 'desktop' => 1])->required(false)->default('zuzu'),
                FormtemplateFieldRequest::new()->type(TextareaField::class)->name('lala2')->required(false)->rows(10),
            ]),
        ])->fake();

        $this->postJson(route('formtemplate.store'))->assertOk();

        $formtemplate = Formtemplate::latest()->first();
        $this->assertEquals('Persönliches', $formtemplate->config['sections'][0]['name']);
        $this->assertEquals('lala1', $formtemplate->config['sections'][0]['fields'][0]['name']);
        $this->assertEquals('TextField', $formtemplate->config['sections'][0]['fields'][0]['type']);
        $this->assertEquals('zuzu', $formtemplate->config['sections'][0]['fields'][0]['default']);
        $this->assertEquals('TextareaField', $formtemplate->config['sections'][0]['fields'][1]['type']);
        $this->assertEquals(false, $formtemplate->config['sections'][0]['fields'][1]['required']);
        $this->assertEquals(['mobile' => 2, 'tablet' => 2, 'desktop' => 1], $formtemplate->config['sections'][0]['fields'][0]['columns']);
        $this->assertEquals(10, $formtemplate->config['sections'][0]['fields'][1]['rows']);
        $this->assertFalse($formtemplate->config['sections'][0]['fields'][0]['required']);
        Event::assertDispatched(Succeeded::class, fn (Succeeded $event) => $event->message === 'Vorlage gespeichert.');
    }

    public function validationDataProvider(): Generator
    {
        yield [FormtemplateRequest::new()->name(''), ['name' => 'Name ist erforderlich.']];
        yield [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->name('')]), ['config.sections.0.name' => 'Sektionsname ist erforderlich.']];
        yield [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
            FormtemplateFieldRequest::new()->name(''),
        ])]), ['config.sections.0.fields.0.name' => 'Feldname ist erforderlich.']];
        yield [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
            FormtemplateFieldRequest::new()->type(''),
        ])]), ['config.sections.0.fields.0.type' => 'Feldtyp ist erforderlich.']];
        yield [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
            FormtemplateFieldRequest::new()->type('aaaaa'),
        ])]), ['config.sections.0.fields.0.type' => 'Feldtyp ist ungültig.']];
        yield [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
            FormtemplateFieldRequest::new()->key(''),
        ])]), ['config.sections.0.fields.0.key' => 'Feldkey ist erforderlich.']];
        yield [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
            FormtemplateFieldRequest::new()->key('a b'),
        ])]), ['config.sections.0.fields.0.key' => 'Feldkey Format ist ungültig.']];
        yield [FormtemplateRequest::new()->sections([FormtemplateSectionRequest::new()->fields([
            FormtemplateFieldRequest::new()->type(TextField::class)->required('la')
        ])]), ['config.sections.0.fields.0.required' => 'Erforderlich muss ein Wahrheitswert sein.']];
    }

    /**
     * @dataProvider validationDataProvider
     * @param array<string, string> $messages
     */
    public function testItValidatesRequests(FormtemplateRequest $request, array $messages): void
    {
        $this->login()->loginNami();
        $request->fake();

        $this->postJson(route('formtemplate.store'))
            ->assertJsonValidationErrors($messages);
    }
}
