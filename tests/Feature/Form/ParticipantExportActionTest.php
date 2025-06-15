<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->setUpForm();
});

it('testItShowsParticipantsAndColumns', function () {
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

    $this->get(route('form.export', ['form' => $form]))->assertDownload('tn-zem-2024.xlsx');
    $contents = Storage::disk('temp')->get('tn-zem-2024.xlsx');
    $this->assertExcelContent('Max', $contents);
    $this->assertExcelContent('A, B', $contents);
    $this->assertExcelContent('Pfadfinder', $contents);
    $this->assertExcelContent('Stufe', $contents);
    $this->assertExcelContent('Abcselect', $contents);
});
