<?php

namespace Tests\Feature\Form;

use App\Form\Fields\CheckboxesField;
use App\Form\Fields\DateField;
use App\Form\Fields\DropdownField;
use App\Form\Fields\GroupField;
use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Group;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ParticipantIndexActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItShowsParticipantsAndColumns(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $group = Group::factory()->innerName('Stamm')->create();
        $form = Form::factory()
            ->has(Participant::factory()->data(['vorname' => 'Max', 'select' => 'A', 'stufe' => 'Pfadfinder', 'test1' => '', 'test2' => '', 'test3' => '', 'birthday' => '1991-04-20', 'bezirk' => $group->id]))
            ->sections([
                FormtemplateSectionRequest::new()->fields([
                    FormtemplateFieldRequest::type(TextField::class)->name('Vorname')->key('vorname'),
                    FormtemplateFieldRequest::type(CheckboxesField::class)->key('select')->options(['A', 'B', 'C']),
                    FormtemplateFieldRequest::type(DropdownField::class)->key('stufe')->options(['Wölfling', 'Jungpfadfinder', 'Pfadfinder']),
                    FormtemplateFieldRequest::type(TextField::class)->name('Test 1')->key('test1'),
                    FormtemplateFieldRequest::type(TextField::class)->name('Test 2')->key('test2'),
                    FormtemplateFieldRequest::type(TextField::class)->name('Test 3')->key('test3'),
                    FormtemplateFieldRequest::type(DateField::class)->name('Geburtsdatum')->key('birthday'),
                    FormtemplateFieldRequest::type(GroupField::class)->name('bezirk')->key('bezirk'),
                ]),
            ])
            ->create();

        $this->callFilter('form.participant.index', [], ['form' => $form])
            ->assertOk()
            ->assertJsonPath('data.0.vorname', 'Max')
            ->assertJsonPath('data.0.stufe', 'Pfadfinder')
            ->assertJsonPath('data.0.bezirk', $group->id)
            ->assertJsonPath('data.0.bezirk_name', 'Stamm')
            ->assertJsonPath('data.0.birthday_human', '20.04.1991')
            ->assertJsonPath('data.0.birthday', '1991-04-20')
            ->assertJsonPath('meta.columns.0.name', 'Vorname')
            ->assertJsonPath('meta.columns.0.base_type', class_basename(TextField::class))
            ->assertJsonPath('meta.columns.0.id', 'vorname')
            ->assertJsonPath('meta.columns.6.display_attribute', 'birthday_human')
            ->assertJsonPath('meta.columns.0.display_attribute', 'vorname')
            ->assertJsonPath('meta.active_columns', ['vorname', 'select', 'stufe', 'test1']);
    }
}
