<?php

namespace Tests\Feature\Form;

use App\Form\Fields\CheckboxField;
use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Scopes\ParticipantFilterScope;
use App\Group;
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
            ->has(Participant::factory()->data(['vorname' => 'Max', 'select' => ['A', 'B'], 'stufe' => 'Pfadfinder', 'test1' => '', 'test2' => '', 'test3' => '', 'birthday' => '1991-04-20', 'bezirk' => $group->id]))
            ->sections([
                FormtemplateSectionRequest::new()->fields([
                    $this->textField('vorname')->name('Vorname'),
                    $this->checkboxesField('select')->options(['A', 'B', 'C']),
                    $this->dropdownField('stufe')->options(['Wölfling', 'Jungpfadfinder', 'Pfadfinder']),
                    $this->textField('test1')->name('Test 1'),
                    $this->textField('test2')->name('Test 2'),
                    $this->textField('test3')->name('Test 3'),
                    $this->dateField('birthday')->name('Geburtsdatum'),
                    $this->groupField('bezirk')->name('bezirk'),
                ]),
            ])
            ->create();

        $this->callFilter('form.participant.index', [], ['form' => $form])
            ->assertOk()
            ->assertJsonPath('data.0.vorname', 'Max')
            ->assertJsonPath('data.0.vorname_display', 'Max')
            ->assertJsonPath('data.0.stufe', 'Pfadfinder')
            ->assertJsonPath('data.0.bezirk', $group->id)
            ->assertJsonPath('data.0.bezirk_display', 'Stamm')
            ->assertJsonPath('data.0.birthday_display', '20.04.1991')
            ->assertJsonPath('data.0.birthday', '1991-04-20')
            ->assertJsonPath('data.0.select', ['A', 'B'])
            ->assertJsonPath('data.0.select_display', 'A, B')
            ->assertJsonPath('data.0.links.destroy', route('participant.destroy', ['participant' => $form->participants->first()]))
            ->assertJsonPath('meta.columns.0.name', 'Vorname')
            ->assertJsonPath('meta.columns.0.base_type', class_basename(TextField::class))
            ->assertJsonPath('meta.columns.0.id', 'vorname')
            ->assertJsonPath('meta.columns.6.display_attribute', 'birthday_display')
            ->assertJsonPath('meta.columns.0.display_attribute', 'vorname_display')
            ->assertJsonPath('meta.form_meta.active_columns', ['vorname', 'select', 'stufe', 'test1'])
            ->assertJsonPath('meta.links.update_form_meta', route('form.update-meta', ['form' => $form]))
            ->assertJsonPath('meta.form_meta.sorting', ['vorname', 'asc']);
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

    public function testItShowsOnlyRootMembers(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();
        Participant::factory()->for($form)->count(2)
            ->has(Participant::factory()->count(3)->for($form), 'children')
            ->create();

        $this->callFilter('form.participant.index', ['parent' => -1], ['form' => $form])->assertJsonCount(2, 'data');
        $this->callFilter('form.participant.index', ['parent' => null], ['form' => $form])->assertJsonCount(8, 'data');
    }

    public function testItShowsChildrenOfParent(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->create();
        $parents = Participant::factory()->for($form)->count(2)
            ->has(Participant::factory()->count(3)->for($form), 'children')
            ->create();

        $this->callFilter('form.participant.index', ['parent' => $parents->get(0)->id], ['form' => $form])->assertJsonCount(3, 'data');
        $this->callFilter('form.participant.index', ['parent' => $parents->get(1)->id], ['form' => $form])->assertJsonCount(3, 'data');
    }

    public function testItPresentsNamiField(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->has(Participant::factory()->data(['mitglieder' => [['id' => 393], ['id' => 394]]]))
            ->has(Participant::factory()->nr(393)->data(['mitglieder' => []]))
            ->has(Participant::factory()->nr(394)->data(['mitglieder' => []]))
            ->sections([
                FormtemplateSectionRequest::new()->fields([
                    $this->namiField('mitglieder'),
                ]),
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
            ->sections([
                FormtemplateSectionRequest::new()->fields([
                    $this->textField('vorname')->name('Vorname'),
                ]),
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
}
