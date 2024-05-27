<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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

        $this->assertTrue($form->config->sections->get(0)->fields->get(0)->maxToday);
    }

    public function testItUpdatesActiveColumnsWhenFieldRemoved(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->textField('firstname'),
                $this->textField('geb'),
                $this->textField('lastname'),
            ])])
            ->create();
        $payload = FormRequest::new()->sections([
            FormtemplateSectionRequest::new()->fields([
                $this->textField('firstname'),
            ])
        ])->create();

        $this->patchJson(route('form.update', ['form' => $form]), $payload)->assertSessionDoesntHaveErrors()->assertOk();
        $this->assertEquals(['firstname'], $form->fresh()->meta['active_columns']);
    }

    public function testItUpdatesActiveState(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();

        $this->patchJson(route('form.update', ['form' => $form]), FormRequest::new()->isActive(false)->create())->assertSessionDoesntHaveErrors()->assertOk();
        $this->assertFalse($form->fresh()->is_active);
        $this->patchJson(route('form.update', ['form' => $form]), FormRequest::new()->isActive(true)->create())->assertSessionDoesntHaveErrors()->assertOk();
        $this->assertTrue($form->fresh()->is_active);
    }

    public function testItUpdatesActiveColumnsWhenFieldsAdded(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([])])
            ->create();
        $payload = FormRequest::new()->sections([
            FormtemplateSectionRequest::new()->fields([
                $this->textField('firstname'),
                $this->textField('geb'),
                $this->textField('lastname'),
            ])
        ])->create();

        $this->patchJson(route('form.update', ['form' => $form]), $payload)->assertSessionDoesntHaveErrors()->assertOk();
        $this->assertEquals(['firstname', 'geb', 'lastname'], $form->fresh()->meta['active_columns']);
    }
}
