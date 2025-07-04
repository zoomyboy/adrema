<?php

namespace Tests\EndToEnd\Form;

use App\Fileshare\Data\FileshareResourceData;
use App\Form\Data\ExportData;
use App\Form\FormSettings;
use App\Form\Models\Form;
use App\Form\Models\Formtemplate;
use App\Form\Models\Participant;
use Carbon\Carbon;
use Tests\Feature\Form\FormtemplateSectionRequest;
use Tests\RequestFactories\EditorRequestFactory;

class FormIndexActionTest extends FormTestCase
{

    public function testItDisplaysForms(): void
    {
        Carbon::setTestNow(Carbon::parse('2023-03-03'));
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->name('lala')
            ->excerpt('fff')
            ->description(EditorRequestFactory::new()->text(10, 'desc'))
            ->from('2023-05-05')
            ->to('2023-06-07')
            ->mailTop(EditorRequestFactory::new()->text(10, 'Guten Tag'))
            ->mailBottom(EditorRequestFactory::new()->text(10, 'Cheers'))
            ->registrationFrom('2023-05-06 04:00:00')
            ->registrationUntil('2023-04-01 05:00:00')
            ->sections([FormtemplateSectionRequest::new()->name('sname')->fields([$this->textField()])])
            ->has(Participant::factory()->count(5))
            ->create();

        sleep(1);
        $this->get(route('form.index'))
            ->assertOk()
            ->assertInertiaPath('data.data.0.name', 'lala')
            ->assertInertiaPath('data.data.0.config.sections.0.name', 'sname')
            ->assertInertiaPath('data.data.0.id', $form->id)
            ->assertInertiaPath('data.data.0.excerpt', 'fff')
            ->assertInertiaPath('data.data.0.description.blocks.0.data.text', 'desc')
            ->assertInertiaPath('data.data.0.mail_top.blocks.0.data.text', 'Guten Tag')
            ->assertInertiaPath('data.data.0.mail_bottom.blocks.0.data.text', 'Cheers')
            ->assertInertiaPath('data.data.0.from_human', '05.05.2023')
            ->assertInertiaPath('data.data.0.to_human', '07.06.2023')
            ->assertInertiaPath('data.data.0.from', '2023-05-05')
            ->assertInertiaPath('data.data.0.participants_count', 5)
            ->assertInertiaPath('data.data.0.to', '2023-06-07')
            ->assertInertiaPath('data.data.0.is_active', true)
            ->assertInertiaPath('data.data.0.is_private', false)
            ->assertInertiaPath('data.data.0.registration_from', '2023-05-06 04:00:00')
            ->assertInertiaPath('data.data.0.needs_prevention', false)
            ->assertInertiaPath('data.data.0.registration_until', '2023-04-01 05:00:00')
            ->assertInertiaPath('data.data.0.links.participant_index', route('form.participant.index', ['form' => $form]))
            ->assertInertiaPath('data.data.0.links.export', route('form.export', ['form' => $form]))
            ->assertInertiaPath('data.meta.links.store', route('form.store'))
            ->assertInertiaPath('data.meta.links.formtemplate_index', route('formtemplate.index'))
            ->assertInertiaPath('data.meta.default.name', '')
            ->assertInertiaPath('data.meta.default.prevention_conditions', ['mode' => 'all', 'ifs' => []])
            ->assertInertiaPath('data.meta.default.prevention_text.version', '1.0')
            ->assertInertiaPath('data.meta.default.description', [])
            ->assertInertiaPath('data.meta.default.excerpt', '')
            ->assertInertiaPath('data.meta.default.is_active', true)
            ->assertInertiaPath('data.meta.default.is_private', false)
            ->assertInertiaPath('data.meta.default.mailattachments', [])
            ->assertInertiaPath('data.meta.default.export', ['root' => null, 'group_by' => null, 'to_group_field' => null])
            ->assertInertiaPath('data.meta.default.config', null)
            ->assertInertiaPath('data.meta.base_url', url(''))
            ->assertInertiaPath('data.meta.namiTypes.0', ['id' => 'Vorname', 'name' => 'Vorname'])
            ->assertInertiaPath('data.meta.specialTypes.0', ['id' => 'Vorname', 'name' => 'Vorname'])
            ->assertInertiaPath('data.meta.section_default.name', '');
    }

    public function testFormtemplatesHaveData(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        Formtemplate::factory()->name('tname')->sections([FormtemplateSectionRequest::new()->name('sname')->fields([
            $this->textField('vorname')
        ])])
            ->mailTop(EditorRequestFactory::new()->text(10, 'lala'))
            ->mailBottom(EditorRequestFactory::new()->text(10, 'lalb'))
            ->create();

        sleep(1);
        $this->get(route('form.index'))
            ->assertOk()
            ->assertInertiaPath('data.meta.templates.0.name', 'tname')
            ->assertInertiaPath('data.meta.templates.0.name', 'tname')
            ->assertInertiaPath('data.meta.templates.0.config.sections.0.name', 'sname')
            ->assertInertiaPath('data.meta.templates.0.config.sections.0.fields.0.key', 'vorname')
            ->assertInertiaPath('data.meta.templates.0.mail_top.blocks.0.data.text', 'lala')
            ->assertInertiaPath('data.meta.templates.0.mail_bottom.blocks.0.data.text', 'lalb');
    }

    public function testItDisplaysExport(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        Form::factory()
            ->name('lala')
            ->export(ExportData::from(['root' => FileshareResourceData::from(['connection_id' => 2, 'resource' => '/dir']), 'group_by' => 'lala', 'to_group_field' => 'abc']))
            ->create();

        sleep(1);
        $this->get(route('form.index'))
            ->assertInertiaPath('data.data.0.export.group_by', 'lala')
            ->assertInertiaPath('data.data.0.export.root.connection_id', 2)
            ->assertInertiaPath('data.data.0.export.root.resource', '/dir')
            ->assertInertiaPath('data.data.0.export.to_group_field', 'abc');
    }

    public function testItHandlesFullTextSearch(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Form::factory()->to(now()->addYear())->name('ZEM 2024')->create();
        Form::factory()->to(now()->addYear())->name('Rover-Spek 2025')->create();

        sleep(1);
        $this->callFilter('form.index', ['search' => 'ZEM'])
            ->assertInertiaCount('data.data', 1);
        $this->callFilter('form.index', [])
            ->assertInertiaCount('data.data', 2);
    }

    public function testItDisplaysParentLinkForFormWithNamiFields(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $form = Form::factory()->fields([$this->namiField('mitglieder')])->create();

        sleep(1);
        $this->callFilter('form.index', [])
            ->assertInertiaPath('data.data.0.has_nami_field', true)
            ->assertInertiaPath('data.data.0.links.participant_root_index', route('form.participant.index', ['form' => $form, 'parent' => -1]))
            ->assertInertiaPath('data.data.0.links.participant_index', route('form.participant.index', ['form' => $form, 'parent' => null]));
    }

    public function testItDisplaysRegisterUrl(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        FormSettings::fake(['registerUrl' => 'https://example.com/form/{slug}/register']);
        Form::factory()->to(now()->addYear())->name('ZEM 2024')->create();

        sleep(1);
        $this->callFilter('form.index', [])->assertInertiaPath('data.data.0.links.frontend', 'https://example.com/form/zem-2024/register');
    }

    public function testItDisplaysCopyUrl(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $form = Form::factory()->create();

        sleep(1);
        $this->callFilter('form.index', [])->assertInertiaPath('data.data.0.links.copy', route('form.copy', $form));
    }

    public function testItDoesntReturnInactiveForms(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Form::factory()->isActive(false)->count(1)->create();
        Form::factory()->isActive(true)->count(2)->create();

        sleep(1);
        $this->callFilter('form.index', [])->assertInertiaCount('data.data', 2);
        $this->callFilter('form.index', ['inactive' => true])->assertInertiaCount('data.data', 3);
        $this->callFilter('form.index', ['inactive' => false])->assertInertiaCount('data.data', 2);
    }

    public function testItOrdersByStartDateDesc(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $form1 = Form::factory()->from(now()->addDays(4))->to(now()->addYear())->create();
        $form2 = Form::factory()->from(now()->addDays(3))->to(now()->addYear())->create();
        $form3 = Form::factory()->from(now()->addDays(2))->to(now()->addYear())->create();

        sleep(1);
        $this->callFilter('form.index', [])
            ->assertInertiaPath('data.data.0.id', $form3->id)
            ->assertInertiaPath('data.data.1.id', $form2->id)
            ->assertInertiaPath('data.data.2.id', $form1->id);
    }

    public function testItShowsPastEvents(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Form::factory()->count(5)->to(now()->subDays(2))->create();
        Form::factory()->count(3)->to(now()->subDays(5))->create();
        Form::factory()->count(2)->to(now()->addDays(3))->create();

        sleep(1);
        $this->callFilter('form.index', ['past' => true])
            ->assertInertiaCount('data.data', 10);
        $this->callFilter('form.index', [])
            ->assertInertiaCount('data.data', 2);
    }
}
