<?php

namespace Tests\Feature\Form;

use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Scopes\ParticipantFilterScope;
use App\Group;
use App\Member\Member;
use Carbon\Carbon;
use Tests\EndToEndTestCase;
use Tests\Lib\CreatesFormFields;

uses(EndToEndTestCase::class);
uses(CreatesFormFields::class);

it('testItShowsParticipantsAndColumns', function () {
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

    sleep(2);
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
        ->assertJsonPath('meta.form_meta.sorting', ['by' => 'id', 'direction' => false])
        ->assertJsonPath('meta.form_config.sections.0.fields.0.key', 'vorname');
});

it('testItShowsEmptyFilters', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->fields([$this->checkboxField('check')->name('Checked')])->create();

    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $form])
        ->assertOk()
        ->assertJsonPath('meta.filters.0.name', 'Checked')
        ->assertJsonPath('meta.filters.0.key', 'check')
        ->assertJsonPath('meta.filters.0.base_type', 'CheckboxField')
        ->assertJsonPath('meta.default_filter_value', ParticipantFilterScope::$nan);

    $this->callFilter('form.participant.index', ['data' => ['check' => null]], ['form' => $form])->assertHasJsonPath('meta.filter.data.check')->assertJsonPath('meta.filter.data.check', null);
    $this->callFilter('form.participant.index', ['data' => ['check' => 'A']], ['form' => $form])->assertJsonPath('meta.filter.data.check', 'A');
    $this->callFilter('form.participant.index', ['data' => []], ['form' => $form])->assertJsonPath('meta.filter.data.check', ParticipantFilterScope::$nan);
});

it('sorts by active colums sorting by default', function (array $sorting, string $by, bool $direction) {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->fields([
        $this->checkboxField('check'),
        $this->checkboxField('vorname'),
    ])->create();
    $form->update(['meta' => ['active_columns' => [], 'sorting' => $sorting]]);

    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $form])
        ->assertOk()
        ->assertJsonPath('meta.filter.sort.by', $by)
        ->assertJsonPath('meta.filter.sort.direction', $direction);
})->with([
    [['by' => 'vorname', 'direction' => true], 'vorname', true],
    [['by' => 'created_at', 'direction' => true], 'created_at', true],
]);


it('testItDisplaysHasNamiField', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->fields([$this->namiField('mitglieder')])->create();
    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $form])->assertJsonPath('meta.has_nami_field', true);
});

it('testItFiltersParticipantsByCheckboxValue', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->fields([$this->checkboxField('check')])
        ->has(Participant::factory()->data(['check' => true])->count(1))
        ->has(Participant::factory()->data(['check' => false])->count(2))
        ->create();

    sleep(2);
    $this->callFilter('form.participant.index', ['data' => []], ['form' => $form])
        ->assertJsonCount(3, 'data');
    $this->callFilter('form.participant.index', ['data' => ['check' => ParticipantFilterScope::$nan]], ['form' => $form])
        ->assertJsonCount(3, 'data');
    $this->callFilter('form.participant.index', ['data' => ['check' => true]], ['form' => $form])
        ->assertJsonCount(1, 'data');
    $this->callFilter('form.participant.index', ['data' => ['check' => false]], ['form' => $form])
        ->assertJsonCount(2, 'data');
});

it('test it handles full text search', function (array $memberAttributes, string $search, bool $includes) {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()
        ->has(Participant::factory()->data(['vorname' => 'Max', 'select' => 'Pfadfinder', ...$memberAttributes]))
        ->fields([
            $this->textField('vorname')->name('Vorname'),
            $this->checkboxesField('select')->options(['Wölflinge', 'Pfadfinder']),
        ])
        ->create();

    sleep(2);
    $this->callFilter('form.participant.index', ['search' => $search], ['form' => $form])
        ->assertJsonCount($includes ? 1 : 0, 'data');
})->with([
    [['vorname' => 'Max'], 'Max', true],
    [['vorname' => 'Jane'], 'Max', false],
    [['select' => 'Pfadfinder'], 'Pfadfinder', true],
    [['select' => 'Pfadfinder'], 'Rov', false],
    [['select' => 'Wölflinge'], 'Wölflinge', true],
    [['select' => 'Wölflinge'], 'Wölf', true],
    [['vorname' => 'Max', 'nachname' => 'Muster'], 'Max Muster', true],
    [['vorname' => 'Max', 'nachname' => 'Muster'], 'Jane Doe', false],
]);

it('testItFiltersParticipantsByDropdownValue', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->fields([$this->dropdownField('drop')->options(['A', 'B'])])
        ->has(Participant::factory()->data(['drop' => null])->count(1))
        ->has(Participant::factory()->data(['drop' => 'A'])->count(2))
        ->has(Participant::factory()->data(['drop' => 'B'])->count(4))
        ->create();

    sleep(2);
    $this->callFilter('form.participant.index', ['data' => ['drop' => ParticipantFilterScope::$nan]], ['form' => $form])
        ->assertJsonCount(7, 'data');
    $this->callFilter('form.participant.index', ['data' => ['drop' => null]], ['form' => $form])
        ->assertJsonCount(1, 'data');
    $this->callFilter('form.participant.index', ['data' => ['drop' => 'A']], ['form' => $form])
        ->assertJsonCount(2, 'data');
    $this->callFilter('form.participant.index', ['data' => ['drop' => 'B']], ['form' => $form])
        ->assertJsonCount(4, 'data');
    $this->callFilter('form.participant.index', ['data' => ['drop' => 'Z*Z']], ['form' => $form])
        ->assertJsonCount(0, 'data');
});

it('testItFiltersParticipantsByRadioValue', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->fields([$this->radioField('drop')->options(['A', 'B'])])
        ->has(Participant::factory()->data(['drop' => null])->count(1))
        ->has(Participant::factory()->data(['drop' => 'A'])->count(2))
        ->has(Participant::factory()->data(['drop' => 'B'])->count(4))
        ->create();

    sleep(2);
    $this->callFilter('form.participant.index', ['data' => ['drop' => ParticipantFilterScope::$nan]], ['form' => $form])
        ->assertJsonCount(7, 'data');
    $this->callFilter('form.participant.index', ['data' => ['drop' => 'A']], ['form' => $form])
        ->assertJsonCount(2, 'data');
    $this->callFilter('form.participant.index', ['data' => ['drop' => 'B']], ['form' => $form])
        ->assertJsonCount(4, 'data');
});

it('testItPresentsNamiField', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()
        ->has(Participant::factory()->data(['mitglieder' => [['id' => 393], ['id' => 394]]]))
        ->has(Participant::factory()->nr(393)->data(['mitglieder' => []]))
        ->has(Participant::factory()->nr(394)->data(['mitglieder' => []]))
        ->fields([
            $this->namiField('mitglieder'),
        ])
        ->create();

    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $form])
        ->assertJsonPath('data.0.mitglieder_display', '393, 394');
});

it('testItShowsRegisteredAtColumnAndAttribute', function () {
    Carbon::setTestNow(Carbon::parse('2023-03-05 06:00:00'));
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()
        ->has(Participant::factory()->data(['vorname' => 'Max']))
        ->fields([
            $this->textField('vorname')->name('Vorname'),
        ])
        ->create();

    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $form])
        ->assertJsonPath('data.0.vorname', 'Max')
        ->assertJsonPath('data.0.vorname_display', 'Max')
        ->assertJsonPath('data.0.created_at', '2023-03-05 06:00:00')
        ->assertJsonPath('data.0.created_at_display', '05.03.2023')
        ->assertJsonPath('meta.columns.1.name', 'Registriert am')
        ->assertJsonPath('meta.columns.1.id', 'created_at')
        ->assertJsonPath('meta.columns.1.display_attribute', 'created_at_display');
});

it('testItShowsOnlyParentParticipantsWhenFilterEnabled', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->create();
    $participant = Participant::factory()
        ->has(Participant::factory()->for($form)->count(2), 'children')
        ->for($form)
        ->create();

    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $form])->assertJsonCount(3, 'data');
    $this->callFilter('form.participant.index', [], ['form' => $form, 'parent' => -1])->assertJsonCount(1, 'data');
    $this->callFilter('form.participant.index', [], ['form' => $form, 'parent' => $participant->id])->assertJsonCount(2, 'data');
});

it('testItShowsChildrenCount', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->create();
    $participant = Participant::factory()
        ->has(Participant::factory()->for($form)->count(2), 'children')
        ->for($form)
        ->create();
    Participant::factory()->for($form)->create();

    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $form, 'parent' => -1])
        ->assertJsonPath('data.0.children_count', 2)
        ->assertJsonPath('data.1.children_count', 0)
        ->assertJsonPath('data.0.links.children', route('form.participant.index', ['form' => $form, 'parent' => $participant->id]))
        ->assertJsonPath('meta.current_page', 1);
    $this->callFilter('form.participant.index', [], ['form' => $form, 'parent' => $participant->id])->assertJsonPath('data.0.children_count', 0);
});

it('testItShowsPreventionState', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $participant = Participant::factory()->data(['vorname' => 'Max'])
        ->for(Member::factory()->defaults()->state(['efz' => null]))
        ->for(Form::factory())
        ->create();

    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $participant->form])
        ->assertJsonPath('data.0.prevention_items.0.letter', 'F')
        ->assertJsonPath('data.0.prevention_items.0.value', false)
        ->assertJsonPath('data.0.prevention_items.0.tooltip', 'erweitertes Führungszeugnis nicht vorhanden');
});

it('doesnt show cancelled participants', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $participant = Participant::factory()->for(Form::factory())->create(['cancelled_at' => now()]);

    sleep(2);
    $this->callFilter('form.participant.index', [], ['form' => $participant->form])
        ->assertJsonCount(0, 'data');
});

it('test it orders participants by value', function (array $values, array $sorting, array $expected) {
    list($key, $direction) = $sorting;
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()
        ->fields([
            $this->textField('vorname')->name('Vorname'),
            $this->checkboxesField('select')->options(['Wölflinge', 'Pfadfinder']),
        ]);
    foreach ($values as $value) {
        $form = $form->has(Participant::factory()->data(['vorname' => 'Max', 'select' => 'Pfadfinder', $key => $value]));
    }
    $form = $form->create();

    sleep(2);
    $response = $this->callFilter('form.participant.index', ['sort' => ['by' => $key, 'direction' => $direction]], ['form' => $form]);
    $response->assertJsonPath('meta.filter.sort.by', $key);
    $response->assertJsonPath('meta.filter.sort.direction', $direction);

    foreach ($expected as $index => $value) {
        $response->assertJsonPath("data.{$index}.{$key}", $value);
    }
})->with([
    [
        ['Anna', 'Sarah', 'Ben'],
        ['vorname', false],
        ['Anna', 'Ben', 'Sarah'],
    ],
    [
        ['Anna', 'Sarah', 'Ben'],
        ['vorname', true],
        ['Sarah', 'Ben', 'Anna'],
    ],
    [
        ['Wölflinge', 'Pfadfinder'],
        ['select', false],
        ['Pfadfinder', 'Wölflinge'],
    ]
]);
