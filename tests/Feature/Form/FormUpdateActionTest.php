<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;

class FormUpdateActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItSetsCustomAttributesOfFields(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();
        $payload = FormRequest::new()->sections([
            FormtemplateSectionRequest::new()->fields([
                $this->dateField()->state(['max_today' => true]),
            ])
        ])->create();

        $this->patchJson(route('form.update', ['form' => $form]), $payload)
            ->assertOk();

        $form = $form->fresh();

        $this->assertTrue(data_get($form->config, 'sections.0.fields.0.max_today'));
    }
}
