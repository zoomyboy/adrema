<?php

namespace Tests\Feature\Form;

use App\Form\Fields\CheckboxesField;
use App\Form\Fields\DropdownField;
use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ParticipantIndexActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItShowsParticipantsAndColumns(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->has(Participant::factory()->data(['vorname' => 'Max', 'select' => 'A', 'stufe' => 'Pfadfinder']))
            ->sections([
                FormtemplateSectionRequest::new()->fields([
                    FormtemplateFieldRequest::type(TextField::class)->name('Vorname')->key('vorname'),
                    FormtemplateFieldRequest::type(CheckboxesField::class)->key('select')->options(['A', 'B', 'C']),
                    FormtemplateFieldRequest::type(DropdownField::class)->key('stufe')->options(['Wölfling', 'Jungpfadfinder', 'Pfadfinder']),
                ]),
            ])
            ->create();

        $this->callFilter('form.participant.index', [], ['form' => $form])
            ->assertOk()
            ->assertJsonPath('data.0.vorname', 'Max')
            ->assertJsonPath('data.0.stufe', 'Pfadfinder')
            ->assertJsonPath('meta.columns.0.name', 'Vorname')
            ->assertJsonPath('meta.columns.0.base_type', class_basename(TextField::class))
            ->assertJsonPath('meta.columns.0.id', 'vorname');
    }
}
