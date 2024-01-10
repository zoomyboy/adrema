<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Formtemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FormIndexActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDisplaysForms(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        Formtemplate::factory()->name('tname')->sections([FormtemplateSectionRequest::new()->name('sname')])->create();
        $form = Form::factory()
            ->name('lala')
            ->excerpt('fff')
            ->description('desc')
            ->from('2023-05-05')
            ->to('2023-06-07')
            ->mailTop('Guten Tag')
            ->mailBottom('Cheers')
            ->registrationFrom('2023-05-06 04:00:00')
            ->registrationUntil('2023-04-01 05:00:00')
            ->sections([FormtemplateSectionRequest::new()->name('sname')->fields([FormtemplateFieldRequest::new()])])
            ->create();

        $this->get(route('form.index'))
            ->assertOk()
            ->assertInertiaPath('data.data.0.config.sections.0.name', 'sname')
            ->assertInertiaPath('data.data.0.name', 'lala')
            ->assertInertiaPath('data.data.0.id', $form->id)
            ->assertInertiaPath('data.data.0.excerpt', 'fff')
            ->assertInertiaPath('data.data.0.description', 'desc')
            ->assertInertiaPath('data.data.0.mail_top', 'Guten Tag')
            ->assertInertiaPath('data.data.0.mail_bottom', 'Cheers')
            ->assertInertiaPath('data.data.0.from_human', '05.05.2023')
            ->assertInertiaPath('data.data.0.to_human', '07.06.2023')
            ->assertInertiaPath('data.data.0.from', '2023-05-05')
            ->assertInertiaPath('data.data.0.to', '2023-06-07')
            ->assertInertiaPath('data.data.0.registration_from', '2023-05-06 04:00:00')
            ->assertInertiaPath('data.data.0.registration_until', '2023-04-01 05:00:00')
            ->assertInertiaPath('data.meta.links.store', route('form.store'))
            ->assertInertiaPath('data.meta.links.formtemplate_index', route('formtemplate.index'))
            ->assertInertiaPath('data.meta.templates.0.name', 'tname')
            ->assertInertiaPath('data.meta.templates.0.config.sections.0.name', 'sname')
            ->assertInertiaPath('data.meta.default.name', '')
            ->assertInertiaPath('data.meta.default.description', '')
            ->assertInertiaPath('data.meta.default.excerpt', '')
            ->assertInertiaPath('data.meta.default.config', null)
            ->assertInertiaPath('data.meta.base_url', url(''))
            ->assertInertiaPath('data.meta.section_default.name', '');
    }
}
