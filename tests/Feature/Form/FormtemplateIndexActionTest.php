<?php

namespace Tests\Feature\Form;

use App\Form\Models\Formtemplate;
use App\Group;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FormtemplateIndexActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDisplaysIndexPage(): void
    {
        $formtemplate = Formtemplate::factory()->sections([FormtemplateSectionRequest::new()->name('sname')])->create();

        $group = Group::factory()->has(Group::factory()->state(['inner_name' => 'child']), 'children')->create(['inner_name' => 'root']);
        $this->login()->loginNami(12345, 'pasword', $group)->withoutExceptionHandling();

        $this->get(route('formtemplate.index'))
            ->assertInertiaPath('data.data.0.links', [
                'update' => route('formtemplate.update', ['formtemplate' => $formtemplate]),
                'destroy' => route('formtemplate.destroy', ['formtemplate' => $formtemplate]),
            ])
            ->assertInertiaPath('data.data.0.config.sections.0.name', 'sname')
            ->assertInertiaPath('data.meta.groups', [
                ['id' => $group->id, 'name' => 'root'],
                ['id' => $group->children->first()->id, 'name' => '-- child'],
            ])
            ->assertInertiaPath('data.meta.base_url', url(''))
            ->assertInertiaPath('data.meta.fields.3', [
                'id' => 'DropdownField',
                'name' => 'Dropdown',
                'default' => [
                    'name' => '',
                    'type' => 'DropdownField',
                    'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
                    'value' => null,
                    'nami_type' => null,
                    'for_members' => true,
                    'special_type' => null,
                    'hint' => '',
                    'intro' => '',
                    'options' => [],
                    'required' => true,
                    'allowcustom' => false,
                ]
            ])
            ->assertInertiaPath('data.meta.fields.9', [
                'id' => 'TextField',
                'name' => 'Text',
                'default' => [
                    'name' => '',
                    'type' => 'TextField',
                    'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
                    'value' => null,
                    'nami_type' => null,
                    'for_members' => true,
                    'special_type' => null,
                    'hint' => '',
                    'intro' => '',
                    'required' => true,
                ]
            ])
            ->assertInertiaPath('data.meta.fields.10', [
                'id' => 'TextareaField',
                'name' => 'Textarea',
                'default' => [
                    'name' => '',
                    'type' => 'TextareaField',
                    'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 6],
                    'value' => '',
                    'nami_type' => null,
                    'for_members' => true,
                    'special_type' => null,
                    'hint' => '',
                    'intro' => '',
                    'rows' => 5,
                    'required' => true,
                ]
            ])
            ->assertInertiaPath('data.meta.default', [
                'name' => '',
                'config' => [
                    'sections' => [],
                ]
            ])
            ->assertInertiaPath('data.meta.links.store', route('formtemplate.store'))
            ->assertInertiaPath('data.meta.links.form_index', route('form.index'))
            ->assertInertiaPath('data.meta.namiTypes.0', ['id' => 'Vorname', 'name' => 'Vorname'])
            ->assertInertiaPath('data.meta.specialTypes.0', ['id' => 'Vorname', 'name' => 'Vorname'])
            ->assertInertiaPath('data.meta.section_default', [
                'name' => '',
                'intro' => '',
                'fields' => [],
            ]);
    }
}
