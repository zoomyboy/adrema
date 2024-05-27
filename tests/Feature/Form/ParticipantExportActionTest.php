<?php

namespace Tests\Feature\Form;

use App\Form\Fields\TextField;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Form\Scopes\ParticipantFilterScope;
use App\Group;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;

class ParticipantExportActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItShowsParticipantsAndColumns(): void
    {
        Storage::fake('temp');
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->has(Participant::factory()->data(['stufe' => 'Pfadfinder', 'vorname' => 'Max', 'select' => ['A', 'B']]))
            ->sections([
                FormtemplateSectionRequest::new()->fields([
                    $this->textField('vorname')->name('Vorname'),
                    $this->checkboxesField('select')->name('Abcselect')->options(['A', 'B', 'C']),
                    $this->dropdownField('stufe')->name('Stufe')->options(['Wölfling', 'Jungpfadfinder', 'Pfadfinder']),
                ]),
            ])
            ->name('ZEM 2024')
            ->create();

        $this->get(route('form.export', ['form' => $form]))->assertDownload('tn-zem-2024.csv');
        $contents = Storage::disk('temp')->get('tn-zem-2024.csv');
        $this->assertTrue(str_contains($contents, 'Max'));
        $this->assertTrue(str_contains($contents, 'A, B'));
        $this->assertTrue(str_contains($contents, 'Pfadfinder'));
        $this->assertTrue(str_contains($contents, 'Stufe'));
        $this->assertTrue(str_contains($contents, 'Abcselect'));
    }
}
