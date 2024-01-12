<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Formtemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FormApiListActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDisplaysForms(): void
    {
        $this->loginNami()->withoutExceptionHandling();
        Formtemplate::factory()->name('tname')->sections([FormtemplateSectionRequest::new()->name('sname')])->create();
        $form = Form::factory()
            ->name('lala 2')
            ->excerpt('fff')
            ->description('desc')
            ->from('2023-05-05')
            ->to('2023-06-07')
            ->sections([FormtemplateSectionRequest::new()->name('sname')->fields([FormtemplateFieldRequest::new()])])
            ->create();

        $this->get('/api/form')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'lala 2')
            ->assertJsonPath('data.0.config.sections.0.name', 'sname')
            ->assertJsonPath('data.0.id', $form->id)
            ->assertJsonPath('data.0.excerpt', 'fff')
            ->assertJsonPath('data.0.description', 'desc')
            ->assertJsonPath('data.0.slug', 'lala-2')
            ->assertJsonPath('data.0.dates', '05.05.2023 - 07.06.2023')
            ->assertJsonPath('data.0.from_human', '05.05.2023')
            ->assertJsonPath('data.0.to_human', '07.06.2023');
    }

    public function testItDisplaysDailyForms(): void
    {
        $this->loginNami()->withoutExceptionHandling();
        Form::factory()
            ->from('2023-05-05')
            ->to('2023-05-05')
            ->create();

        $this->get('/api/form')
            ->assertJsonPath('data.0.dates', '05.05.2023');
    }
}
