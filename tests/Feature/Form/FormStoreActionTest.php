<?php

namespace Tests\Feature\Form;

use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Lib\Events\Succeeded;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Generator;
use Illuminate\Support\Facades\Storage;
use Tests\RequestFactories\EditorRequestFactory;

class FormStoreActionTest extends TestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('temp');
    }

    public function testItStoresForm(): void
    {
        Event::fake([Succeeded::class]);
        $this->login()->loginNami()->withoutExceptionHandling();
        $description = EditorRequestFactory::new()->text(10, 'Lorem');
        FormRequest::new()
            ->name('formname')
            ->description($description->create())
            ->excerpt('avff')
            ->registrationFrom('2023-05-04 01:00:00')->registrationUntil('2023-07-07 01:00:00')->from('2023-07-07')->to('2023-07-08')
            ->mailTop('Guten Tag')
            ->mailBottom('Viele Grüße')
            ->headerImage('htzz.jpg')
            ->sections([FormtemplateSectionRequest::new()->name('sname')->fields([FormtemplateFieldRequest::type(TextField::class)])])
            ->fake();

        $this->postJson(route('form.store'))->assertOk();

        $form = Form::latest()->first();
        $this->assertEquals('sname', $form->config['sections'][0]['name']);
        $this->assertEquals('formname', $form->name);
        $this->assertEquals('avff', $form->excerpt);
        $this->assertEquals($description->paragraphBlock(10, 'Lorem'), $form->description);
        $this->assertEquals('Guten Tag', $form->mail_top);
        $this->assertEquals('Viele Grüße', $form->mail_bottom);
        $this->assertEquals('2023-05-04 01:00', $form->registration_from->format('Y-m-d H:i'));
        $this->assertEquals('2023-07-07 01:00', $form->registration_until->format('Y-m-d H:i'));
        $this->assertEquals('2023-07-07', $form->from->format('Y-m-d'));
        $this->assertEquals('2023-07-08', $form->to->format('Y-m-d'));
        $this->assertCount(1, $form->getMedia('headerImage'));
        $this->assertEquals('formname.jpg', $form->getMedia('headerImage')->first()->file_name);
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
        yield [FormRequest::new()->description(null), ['description.blocks' => 'Beschreibung ist erforderlich.']];
        yield [FormRequest::new()->state(['from' => null]), ['from' => 'Start ist erforderlich']];
        yield [FormRequest::new()->state(['to' => null]), ['to' => 'Ende ist erforderlich']];
        yield [FormRequest::new()->state(['header_image' => null]), ['header_image' => 'Bild ist erforderlich']];
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
