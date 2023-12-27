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
            ->sections([FormtemplateSectionRequest::new()->name('sname')->fields([FormtemplateFieldRequest::new()])])
            ->fake();

        $this->postJson(route('form.store'))->assertOk();

        $form = Form::latest()->first();
        $this->assertEquals('sname', $form->config['sections'][0]['name']);
        $this->assertEquals('formname', $form->name);
        $this->assertEquals('avff', $form->excerpt);
        $this->assertEquals('lala ggg', $form->description);
        Event::assertDispatched(Succeeded::class, fn (Succeeded $event) => $event->message === 'Formular gespeichert.');
    }

    public function validationDataProvider(): Generator
    {
        yield [FormRequest::new()->name(''), ['name' => 'Name ist erforderlich.']];
        yield [FormRequest::new()->excerpt(''), ['excerpt' => 'Auszug ist erforderlich.']];
        yield [FormRequest::new()->description(''), ['description' => 'Beschreibung ist erforderlich.']];
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
