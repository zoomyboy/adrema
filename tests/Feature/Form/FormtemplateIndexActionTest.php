<?php

namespace Tests\Feature\Form;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FormtemplateIndexActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDisplaysIndexPage(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $this->get(route('formtemplate.index'))
            ->assertInertiaPath('data.meta.fields.0', [
                'id' => 'TextField',
                'name' => 'Text',
                'default' => [
                    'name' => '',
                    'type' => 'TextField',
                    'columns' => ['mobile' => 2, 'tablet' => 4, 'desktop' => 12],
                    'default' => '',
                    'required' => false,
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
