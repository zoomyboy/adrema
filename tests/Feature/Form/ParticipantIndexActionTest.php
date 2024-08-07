<?php

namespace Tests\Feature\Form;

use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Scopes\ParticipantFilterScope;
use App\Group;
use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantIndexActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItShowsParticipantsAndColumns(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $group = Group::factory()->innerName('Stamm')->create();
        $form = Form::factory()
            ->has(Participant::factory()->state(['member_id' => 55])->data(['vorname' => 'Max', 'select' => ['A', 'B'], 'stufe' => 'Pfadfinder', 'test1' => '', 'test2' => '', 'test3' => '', 'birthday' => '1991-04-20', 'bezirk' => $group->id]))
            ->fields([
                $this->textField('vorname')->name('Vorname'),
                $this->checkboxesField('select')->options(['A', 'B', 'C']),
                $this->dropdownField('stufe')->options(['Wölfling', 'Jungpfadfinder', 'Pfadfinder']),
                $this->textField('test1')->name('Test 1'),
                $this->textField('test2')->name('Test 2'),
                $this->textField('test3')->name('Test 3'),
                $this->dateField('birthday')->name('Geburtsdatum'),
                $this->groupField('bezirk')->name('bezirk'),
            ])
            ->create();

        $this->callFilter('form.participant.index', [], ['form' => $form])
            ->assertOk()
            ->assertJsonPath('data.0.id', $form->participants->first()->id)
            ->assertJsonPath('data.0.vorname', 'Max')
            ->assertJsonPath('data.0.vorname_display', 'Max')
            ->assertJsonPath('data.0.stufe', 'Pfadfinder')
            ->assertJsonPath('data.0.bezirk', $group->id)
            ->assertJsonPath('data.0.member_id', 55)
            ->assertJsonPath('data.0.bezirk_display', 'Stamm')
            ->assertJsonPath('data.0.birthday_display', '20.04.1991')
            ->assertJsonPath('data.0.birthday', '1991-04-20')
            ->assertJsonPath('data.0.select', ['A', 'B'])
            ->assertJsonPath('data.0.select_display', 'A, B')
            ->assertJsonPath('data.0.links.destroy', route('participant.destroy', ['participant' => $form->participants->first()]))
            ->assertJsonPath('data.0.links.assign', route('participant.assign', ['participant' => $form->participants->first()]))
            ->assertJsonPath('data.0.links.fields', route('participant.fields', ['participant' => $form->participants->first()]))
            ->assertJsonPath('data.0.links.update', route('participant.update', ['participant' => $form->participants->first()]))
            ->assertJsonPath('meta.columns.0.name', 'Vorname')
            ->assertJsonPath('meta.columns.0.base_type', class_basename(TextField::class))
            ->assertJsonPath('meta.columns.0.id', 'vorname')
            ->assertJsonPath('meta.columns.6.display_attribute', 'birthday_display')
            ->assertJsonPath('meta.columns.0.display_attribute', 'vorname_display')
            ->assertJsonPath('meta.form_meta.active_columns', ['vorname', 'select', 'stufe', 'test1'])
            ->assertJsonPath('meta.has_nami_field', false)
            ->assertJsonPath('meta.links.update_form_meta', route('form.update-meta', ['form' => $form]))
            ->assertJsonPath('meta.links.store_participant', route('form.participant.store', ['form' => $form]))
            ->assertJsonPath('meta.form_meta.sorting', ['vorname', 'asc'])
            ->assertJsonPath('meta.form_config.sections.0.fields.0.key', 'vorname');
    }

    public function testItShowsEmptyFilters(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->fields([$this->checkboxField('check')->name('Checked')])->create();

        $this->callFilter('form.participant.index', [], ['form' => $form])
            ->assertOk()
            ->assertJsonPath('meta.filters.0.name', 'Checked')
            ->assertJsonPath('meta.filters.0.key', 'check')
            ->assertJsonPath('meta.filters.0.base_type', 'CheckboxField')
            ->assertJsonPath('meta.default_filter_value', ParticipantFilterScope::$nan);

        $this->callFilter('form.participant.index', ['data' => ['check' => null]], ['form' => $form])->assertHasJsonPath('meta.filter.data.check')->assertJsonPath('meta.filter.data.check', null);
        $this->callFilter('form.participant.index', ['data' => ['check' => 'A']], ['form' => $form])->assertJsonPath('meta.filter.data.check', 'A');
        $this->callFilter('form.participant.index', ['data' => []], ['form' => $form])->assertJsonPath('meta.filter.data.check', ParticipantFilterScope::$nan);
    }

    public function testItDisplaysHasNamiField(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->fields([$this->namiField('mitglieder')])->create();
        $this->callFilter('form.participant.index', [], ['form' => $form])->assertJsonPath('meta.has_nami_field', true);
    }

    public function testItFiltersParticipantsByCheckboxValue(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->fields([$this->checkboxField('check')])
            ->has(Participant::factory()->data(['check' => true])->count(1))
            ->has(Participant::factory()->data(['check' => false])->count(2))
            ->create();

        $this->callFilter('form.participant.index', ['data' => ['check' => ParticipantFilterScope::$nan]], ['form' => $form])
            ->assertJsonCount(3, 'data');
        $this->callFilter('form.participant.index', ['data' => ['check' => true]], ['form' => $form])
            ->assertJsonCount(1, 'data');
        $this->callFilter('form.participant.index', ['data' => ['check' => false]], ['form' => $form])
            ->assertJsonCount(2, 'data');
    }

    public function testItFiltersParticipantsByDropdownValue(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->fields([$this->dropdownField('drop')->options(['A', 'B'])])
            ->has(Participant::factory()->data(['drop' => null])->count(1))
            ->has(Participant::factory()->data(['drop' => 'A'])->count(2))
            ->has(Participant::factory()->data(['drop' => 'B'])->count(4))
            ->create();

        $this->callFilter('form.participant.index', ['data' => ['drop' => ParticipantFilterScope::$nan]], ['form' => $form])
            ->assertJsonCount(7, 'data');
        $this->callFilter('form.participant.index', ['data' => ['drop' => null]], ['form' => $form])
            ->assertJsonCount(1, 'data');
        $this->callFilter('form.participant.index', ['data' => ['drop' => 'A']], ['form' => $form])
            ->assertJsonCount(2, 'data');
        $this->callFilter('form.participant.index', ['data' => ['drop' => 'B']], ['form' => $form])
            ->assertJsonCount(4, 'data');
    }

    public function testItFiltersParticipantsByRadioValue(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->fields([$this->radioField('drop')->options(['A', 'B'])])
            ->has(Participant::factory()->data(['drop' => null])->count(1))
            ->has(Participant::factory()->data(['drop' => 'A'])->count(2))
            ->has(Participant::factory()->data(['drop' => 'B'])->count(4))
            ->create();

        $this->callFilter('form.participant.index', ['data' => ['drop' => ParticipantFilterScope::$nan]], ['form' => $form])
            ->assertJsonCount(7, 'data');
        $this->callFilter('form.participant.index', ['data' => ['drop' => 'A']], ['form' => $form])
            ->assertJsonCount(2, 'data');
        $this->callFilter('form.participant.index', ['data' => ['drop' => 'B']], ['form' => $form])
            ->assertJsonCount(4, 'data');
    }

    public function testItPresentsNamiField(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->has(Participant::factory()->data(['mitglieder' => [['id' => 393], ['id' => 394]]]))
            ->has(Participant::factory()->nr(393)->data(['mitglieder' => []]))
            ->has(Participant::factory()->nr(394)->data(['mitglieder' => []]))
            ->fields([
                $this->namiField('mitglieder'),
            ])
            ->create();

        $this->callFilter('form.participant.index', [], ['form' => $form])
            ->assertJsonPath('data.0.mitglieder_display', '393, 394');
    }

    public function testItShowsRegisteredAtColumnAndAttribute(): void
    {
        Carbon::setTestNow(Carbon::parse('2023-03-05 06:00:00'));
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->has(Participant::factory()->data(['vorname' => 'Max']))
            ->fields([
                $this->textField('vorname')->name('Vorname'),
            ])
            ->create();

        $this->callFilter('form.participant.index', [], ['form' => $form])
            ->assertJsonPath('data.0.vorname', 'Max')
            ->assertJsonPath('data.0.vorname_display', 'Max')
            ->assertJsonPath('data.0.created_at', '2023-03-05 06:00:00')
            ->assertJsonPath('data.0.created_at_display', '05.03.2023')
            ->assertJsonPath('meta.columns.1.name', 'Registriert am')
            ->assertJsonPath('meta.columns.1.id', 'created_at')
            ->assertJsonPath('meta.columns.1.display_attribute', 'created_at_display');
    }

    public function testItShowsOnlyParentParticipantsWhenFilterEnabled(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();
        $participant = Participant::factory()
            ->has(Participant::factory()->for($form)->count(2), 'children')
            ->for($form)
            ->create();

        $this->callFilter('form.participant.index', [], ['form' => $form])->assertJsonCount(3, 'data');
        $this->callFilter('form.participant.index', [], ['form' => $form, 'parent' => -1])->assertJsonCount(1, 'data');
        $this->callFilter('form.participant.index', [], ['form' => $form, 'parent' => $participant->id])->assertJsonCount(2, 'data');
    }

    public function testItShowsChildrenCount(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();
        $participant = Participant::factory()
            ->has(Participant::factory()->for($form)->count(2), 'children')
            ->for($form)
            ->create();
        Participant::factory()->for($form)->create();

        $this->callFilter('form.participant.index', [], ['form' => $form, 'parent' => -1])
            ->assertJsonPath('data.0.children_count', 2)
            ->assertJsonPath('data.1.children_count', 0)
            ->assertJsonPath('data.0.links.children', route('form.participant.index', ['form' => $form, 'parent' => $participant->id]))
            ->assertJsonPath('meta.current_page', 1);
        $this->callFilter('form.participant.index', [], ['form' => $form, 'parent' => $participant->id])->assertJsonPath('data.0.children_count', 0);
    }

    public function testItShowsPreventionState(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $participant = Participant::factory()->data(['vorname' => 'Max'])
            ->for(Member::factory()->defaults()->state(['efz' => null]))
            ->for(Form::factory())
            ->create();

        $this->callFilter('form.participant.index', [], ['form' => $participant->form])
            ->assertJsonPath('data.0.prevention_items.0.letter', 'F')
            ->assertJsonPath('data.0.prevention_items.0.value', false)
            ->assertJsonPath('data.0.prevention_items.0.tooltip', 'erweitertes Führungszeugnis nicht vorhanden');
    }
}
