<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->fakeMessages();
    test()->setUpForm();
});

dataset('media', fn () => [
    ['mailattachments'],
    ['headerImage'],
]);

it('copies a form', function () {
    test()->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->name('Lager')->create();

    test()->post(route('form.copy', ['form' => $form]))
        ->assertOk();

    test()->assertDatabaseCount('forms', 2);

    $newForm = Form::where('name', 'Lager - Kopie')->firstOrFail();
    test()->assertEquals(
        Arr::except($form->fresh()->toArray(), ['id', 'name', 'slug', 'created_at', 'updated_at', 'is_active']),
        Arr::except($newForm->fresh()->toArray(), ['id', 'name', 'slug', 'created_at', 'updated_at', 'is_active'])
    );
});

it('copies the forms media', function (string $collectionName) {
    test()->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->withImage($collectionName, 'lala.jpg')->name('Lager')->create();

    test()->post(route('form.copy', ['form' => $form]))->assertOk();

    test()->assertDatabaseCount('forms', 2);

    $newForm = Form::where('name', 'Lager - Kopie')->firstOrFail();
    test()->assertEquals($form->getMedia($collectionName)->first()->name, $newForm->getMedia($collectionName)->first()->name);
    test()->assertNotEquals($form->getMedia($collectionName)->first()->id, $newForm->getMedia($collectionName)->first()->id);
    test()->assertNotEquals($form->getMedia($collectionName)->first()->getFullUrl(), $newForm->getMedia($collectionName)->first()->getFullUrl());
})->with('media');

it('deactivates a copied form', function () {
    test()->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->name('Lager')->create(['is_active' => true]);

    test()->post(route('form.copy', ['form' => $form]))->assertOk();

    $newForm = Form::where('name', 'Lager - Kopie')->firstOrFail();
    test()->assertEquals(false, $newForm->is_active);
});

it('shows success message', function () {
    test()->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->create();

    test()->post(route('form.copy', ['form' => $form]))->assertOk();

    test()->assertSuccessMessage('Veranstaltung kopiert.');
});
