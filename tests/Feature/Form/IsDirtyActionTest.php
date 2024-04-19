<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IsDirtyActionTest extends FormTestCase
{
    use DatabaseTransactions;

    public function testItChecksIfFormIsDirty(): void
    {
        $this->login()->loginNami();
        $form = Form::factory()->fields([
            $this->textField(),
        ])->create();

        $this->postJson(route('form.is-dirty', ['form' => $form]), ['config' => $form->config->toArray()])->assertJsonPath('result', false);

        $modifiedConfig = $form->config->toArray();
        data_set($modifiedConfig, 'sections.0.name', 'mod');
        $this->postJson(route('form.is-dirty', ['form' => $form]), ['config' => $modifiedConfig])->assertJsonPath('result', true);
    }
}
