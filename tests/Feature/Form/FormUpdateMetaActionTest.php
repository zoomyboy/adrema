<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FormUpdateMetaActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItUpdatesMetaOfForm(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->sections([FormtemplateSectionRequest::new()->fields([
                $this->textField('textone'),
                $this->dropdownField('texttwo'),
            ])])->create();

        $this->patchJson(route('form.update-meta', ['form' => $form]), [
            'active_columns' => ['textone'],
            'sorting' => ['by' => 'textone', 'direction' => false],
        ])->assertOk()
            ->assertJsonPath('active_columns.0', 'textone')
            ->assertJsonPath('sorting.by', 'textone');

        $form = Form::latest()->first();
        $this->assertEquals(['by' => 'textone', 'direction' => false], $form->meta['sorting']);
        $this->assertEquals(['textone'], $form->meta['active_columns']);
    }

    public function testItCanSetCreatedAtMeta(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();

        $this->patchJson(route('form.update-meta', ['form' => $form]), [
            'active_columns' => ['created_at'],
            'sorting' => ['by' => 'textone', 'direction' => false],
        ])->assertOk();

        $form = Form::latest()->first();
        $this->assertEquals(['by' => 'textone', 'direction' => false], $form->fresh()->meta['sorting']);
        $this->assertEquals(['created_at'], $form->fresh()->meta['active_columns']);
    }
}
