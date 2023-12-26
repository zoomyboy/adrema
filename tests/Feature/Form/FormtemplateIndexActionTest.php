<?php

namespace Tests\Feature\Form;

use App\Form\Models\Formtemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FormtemplateIndexActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDisplaysIndexPage(): void
    {
        $formtemplate = Formtemplate::factory()->create();

        $this->login()->loginNami()->withoutExceptionHandling();

        $this->get(route('formtemplate.index'))
            ->assertInertiaPath('data.data.0.links', [
                'update' => route('formtemplate.update', ['formtemplate' => $formtemplate]),
            ])
            ->assertInertiaPath('data.meta.fields.2', [
                'id' => 'DropdownField',
                'name' => 'Dropdown',
                'default' => [
                    'name' => '',
                    'type' => 'DropdownField',
                    'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
                    'default' => null,
                    'required' => false,
                    'options' => [],
                ]
            ])
            ->assertInertiaPath('data.meta.fields.4', [
                'id' => 'TextField',
                'name' => 'Text',
                'default' => [
                    'name' => '',
                    'type' => 'TextField',
                    'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
                    'default' => '',
                    'required' => false,
                ]
            ])
            ->assertInertiaPath('data.meta.fields.5', [
                'id' => 'TextareaField',
                'name' => 'Textarea',
                'default' => [
                    'name' => '',
                    'type' => 'TextareaField',
                    'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
                    'default' => '',
                    'required' => false,
                    'rows' => 5,
                ]
            ])
            ->assertInertiaPath('data.meta.default', [
                'name' => '',
                'config' => [
                    'sections' => [],
                ]
            ])
            ->assertInertiaPath('data.meta.links.store', route('formtemplate.store'))
            ->assertInertiaPath('data.meta.section_default', [
                'name' => '',
                'intro' => '',
                'fields' => [],
            ]);
    }
}
