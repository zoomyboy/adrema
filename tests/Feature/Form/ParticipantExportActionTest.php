<?php

namespace Tests\Feature\Form;

use DB;
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
        ->fields([
            $this->textField('vorname')->name('Vorname'),
            $this->checkboxesField('select')->name('Abcselect')->options(['A', 'B', 'C']),
            $this->dropdownField('stufe')->name('Stufe')->options(['Wölfling', 'Jungpfadfinder', 'Pfadfinder']),
        ])
        ->name('ZEM 2024')
        ->create();
    DB::table('participants')->where('id', $form->participants->first()->id)->update(['id' => 9909]);

    $this->get(route('form.export', ['form' => $form]))->assertDownload('tn-zem-2024.xlsx');
    $contents = Storage::disk('temp')->get('tn-zem-2024.xlsx');
    $this->assertExcelContent('Max', $contents);
    $this->assertExcelContent('A, B', $contents);
    $this->assertExcelContent('Pfadfinder', $contents);
    $this->assertExcelContent('Stufe', $contents);
    $this->assertExcelContent('Abcselect', $contents);
    $this->assertExcelContent('9909', $contents);
});

it('shows cancelled at', function () {
    Storage::fake('temp');
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->name('ZEM 2024')
        ->has(Participant::factory()->state(['cancelled_at' => now()->subWeek()]))
        ->create();

    $this->get(route('form.export', ['form' => $form]))->assertDownload('tn-zem-2024.xlsx');
    $contents = Storage::disk('temp')->get('tn-zem-2024.xlsx');
    $this->assertExcelContent(now()->subWeek()->format('d.m.Y'), $contents);
});
