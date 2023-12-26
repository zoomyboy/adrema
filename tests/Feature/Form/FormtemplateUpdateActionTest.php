<?php

namespace Tests\Feature\Form;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FormtemplateUpdateActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItStoresTemplates(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $this->postJson(route('formtemplate.store'), [
            'name' => 'Testname',
            'config' => [
                'sections' => [
                    ['name' => 'Persönliches', 'fields' => []]
                ]
            ]
        ])->assertOk();

        $this->assertDatabaseHas('formtemplates', [
            'name' => 'Testname',
            'config' => json_encode(['sections' => [['name' => 'Persönliches', 'fields' => []]]]),
        ]);
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
