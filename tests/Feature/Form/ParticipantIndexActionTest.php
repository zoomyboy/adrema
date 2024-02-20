<?php

namespace Tests\Feature\Form;

use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Group;
use App\Member\Member;
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
            ->assertJsonPath('meta.columns.0.name', 'Vorname')
            ->assertJsonPath('meta.columns.0.base_type', class_basename(TextField::class))
            ->assertJsonPath('meta.columns.0.id', 'vorname')
            ->assertJsonPath('meta.columns.6.display_attribute', 'birthday_display')
            ->assertJsonPath('meta.columns.0.display_attribute', 'vorname_display')
            ->assertJsonPath('meta.active_columns', ['vorname', 'select', 'stufe', 'test1']);
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
}
