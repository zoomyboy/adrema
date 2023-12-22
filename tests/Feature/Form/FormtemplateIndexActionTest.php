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
            ])
            ->assertInertiaPath('data.meta.default', [
                'name' => '',
                'config' => [
                    'sections' => [],
                ]
            ])
            ->assertInertiaPath('data.meta.links.store', route('formtemplate.store'))
            ->assertInertiaPath('data.meta.section_default', [
                'fields' => [],
            ]);
    }
}
