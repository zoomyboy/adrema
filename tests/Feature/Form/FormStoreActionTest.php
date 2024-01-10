<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Lib\Events\Succeeded;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Generator;

class FormStoreActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItStoresForm(): void
    {
        Event::fake([Succeeded::class]);
        $this->login()->loginNami()->withoutExceptionHandling();
        FormRequest::new()
            ->name('formname')
            ->description('lala ggg')
            ->excerpt('avff')
            ->registrationFrom('2023-05-04 01:00:00')->registrationUntil('2023-07-07 01:00:00')->from('2023-07-07')->to('2023-07-08')
            ->mailTop('Guten Tag')
            ->mailBottom('Viele Grüße')
            ->sections([FormtemplateSectionRequest::new()->name('sname')->fields([FormtemplateFieldRequest::new()])])
            ->fake();

        $this->postJson(route('form.store'))->assertOk();

        $form = Form::latest()->first();
        $this->assertEquals('sname', $form->config['sections'][0]['name']);
        $this->assertEquals('formname', $form->name);
        $this->assertEquals('avff', $form->excerpt);
        $this->assertEquals('lala ggg', $form->description);
        $this->assertEquals('Guten Tag', $form->mail_top);
        $this->assertEquals('Viele Grüße', $form->mail_bottom);
        $this->assertEquals('2023-05-04 01:00', $form->registration_from->format('Y-m-d H:i'));
        $this->assertEquals('2023-07-07 01:00', $form->registration_until->format('Y-m-d H:i'));
        $this->assertEquals('2023-07-07', $form->from->format('Y-m-d'));
        $this->assertEquals('2023-07-08', $form->to->format('Y-m-d'));
        Event::assertDispatched(Succeeded::class, fn (Succeeded $event) => $event->message === 'Veranstaltung gespeichert.');
    }

    public function testRegistrationDatesCanBeNull(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $this->postJson(route('form.store'), FormRequest::new()->registrationFrom(null)->registrationUntil(null)->create())->assertOk();

        $this->assertDatabaseHas('forms', [
            'registration_until' => null,
            'registration_from' => null,
        ]);
    }

    public function validationDataProvider(): Generator
    {
        yield [FormRequest::new()->name(''), ['name' => 'Name ist erforderlich.']];
        yield [FormRequest::new()->excerpt(''), ['excerpt' => 'Auszug ist erforderlich.']];
        yield [FormRequest::new()->description(''), ['description' => 'Beschreibung ist erforderlich.']];
        yield [FormRequest::new()->state(['from' => null]), ['from' => 'Start ist erforderlich']];
        yield [FormRequest::new()->state(['to' => null]), ['to' => 'Ende ist erforderlich']];
    }

    /**
     * @dataProvider validationDataProvider
     * @param array<string, string> $messages
     */
    public function testItValidatesRequests(FormRequest $request, array $messages): void
    {
        $this->login()->loginNami();
        $request->fake();

        $this->postJson(route('form.store'))->assertJsonValidationErrors($messages);
    }
}