<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Formtemplate;
use App\Lib\Events\Succeeded;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\RequestFactories\EditorRequestFactory;
use Tests\TestCase;

class FormtemplateUpdateActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItUpdatesTemplates(): void
    {
        Event::fake([Succeeded::class]);
        $this->login()->loginNami()->withoutExceptionHandling();
        $formtemplate = Formtemplate::factory()->create();
        FormtemplateRequest::new()->name('testname')->fake();

        $this->patchJson(route('formtemplate.update', ['formtemplate' => $formtemplate]))
            ->assertOk();

        $this->assertDatabaseHas('formtemplates', [
            'name' => 'Testname',
        ]);
        Event::assertDispatched(Succeeded::class, fn (Succeeded $event) => $event->message === 'Vorlage aktualisiert.');
    }

    public function testItUpdatesTexts(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $formtemplate = Formtemplate::factory()->create();
        $payload = FormtemplateRequest::new()
            ->mailTop(EditorRequestFactory::new()->text(10, 'lala'))
            ->mailBottom(EditorRequestFactory::new()->text(10, 'lalb'))
            ->create();

        $this->patchJson(route('formtemplate.update', ['formtemplate' => $formtemplate]), $payload)
            ->assertOk();

        $this->assertEquals('lala', Formtemplate::first()->mail_top->blocks[0]['data']['text']);
        $this->assertEquals('lalb', Formtemplate::first()->mail_bottom->blocks[0]['data']['text']);
    }

    public function testNameIsRequired(): void
    {
        $this->login()->loginNami();

        $this->postJson(route('formtemplate.store'), [
            'name' => '',
            'config' => [
                'sections' => []
            ]
        ])->assertJsonValidationErrors(['name' => 'Name ist erforderlich']);
    }
}
