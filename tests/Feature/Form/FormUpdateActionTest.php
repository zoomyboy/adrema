<?php

namespace Tests\Feature\Form;

use App\Fileshare\Data\FileshareResourceData;
use App\Form\Data\ExportData;
use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;

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

    public function testItClearsFrontendCacheWhenFormUpdated(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();

        $this->patchJson(route('form.update', ['form' => $form]), FormRequest::new()->create());

        $this->assertFrontendCacheCleared();
    }

    public function testItUpdatesExport(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $form = Form::factory()->create();
        $this->patchJson(route('form.update', ['form' => $form]), FormRequest::new()->export(ExportData::from(['root' => FileshareResourceData::from(['connection_id' => 2, 'resource' => '/dir']), 'group_by' => 'lala', 'to_group_field' => 'abc']))->create());

        $this->assertEquals(2, $form->fresh()->export->root->connectionId);
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

    public function testItUpdatesIntroOfSections(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->intro('aaa')])
            ->create();
        $payload = FormRequest::new()->sections([
            FormtemplateSectionRequest::new()->intro('aaa')
        ])->create();

        $this->patchJson(route('form.update', ['form' => $form]), $payload)->assertSessionDoesntHaveErrors()->assertOk();
        $this->assertEquals('aaa', $form->fresh()->config->sections[0]->intro);
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

    public function testItUpdatesPrivateState(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();

        $this->patchJson(route('form.update', ['form' => $form]), FormRequest::new()->isPrivate(false)->create())->assertSessionDoesntHaveErrors()->assertOk();
        $this->assertFalse($form->fresh()->is_private);
        $this->patchJson(route('form.update', ['form' => $form]), FormRequest::new()->isPrivate(true)->create())->assertSessionDoesntHaveErrors()->assertOk();
        $this->assertTrue($form->fresh()->is_private);
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
