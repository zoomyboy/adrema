<?php

namespace Tests\EndToEnd\Form;

use App\Form\Models\Form;
use App\Membership\TestersBlock;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Form\FormtemplateSectionRequest;
use Tests\RequestFactories\EditorRequestFactory;
use Tests\TestCase;

uses(FormTestCase::class);
uses(DatabaseTransactions::class);

it('testItDisplaysForms', function () {
    Carbon::setTestNow(Carbon::parse('2023-03-02'));
    Storage::fake('temp');
    $this->loginNami()->withoutExceptionHandling();
    $form = Form::factory()
        ->name('lala 2')
        ->excerpt('fff')
        ->withImage('headerImage', 'lala-2.jpg')
        ->description(EditorRequestFactory::new()->text(10, 'desc'))
        ->from('2023-05-05')
        ->to('2023-06-07')
        ->sections([FormtemplateSectionRequest::new()->name('sname')])
        ->create();

    sleep(1);
    $this->get('/api/form?perPage=15')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'lala 2')
        ->assertJsonPath('data.0.config.sections.0.name', 'sname')
        ->assertJsonPath('data.0.id', $form->id)
        ->assertJsonPath('data.0.excerpt', 'fff')
        ->assertJsonPath('data.0.description.blocks.0.data.text', 'desc')
        ->assertJsonPath('data.0.slug', 'lala-2')
        ->assertJsonPath('data.0.image', $form->getMedia('headerImage')->first()->getFullUrl('square'))
        ->assertJsonPath('data.0.dates', '05.05.2023 - 07.06.2023')
        ->assertJsonPath('data.0.from_human', '05.05.2023')
        ->assertJsonPath('data.0.to_human', '07.06.2023')
        ->assertJsonPath('meta.per_page', 15)
        ->assertJsonPath('meta.base_url', url(''))
        ->assertJsonPath('meta.total', 1);
});

it('testItDisplaysDefaultValueOfField', function () {
    Storage::fake('temp');
    $this->loginNami()->withoutExceptionHandling();
    Form::factory()->withImage('headerImage', 'lala-2.jpg')
        ->sections([FormtemplateSectionRequest::new()->fields([$this->textField()])])
        ->create();

    sleep(1);
    $this->get('/api/form?perPage=15')->assertJsonPath('data.0.config.sections.0.fields.0.value', null);
});

it('testItDisplaysRemoteGroups', function () {
    $this->loginNami()->withoutExceptionHandling();
    Subactivity::factory()->inNami(1)->name('Wölfling')->ageGroup(true)->create();
    Subactivity::factory()->inNami(50)->name('Biber')->ageGroup(false)->create();
    Subactivity::factory()->name('Lager')->ageGroup(true)->create();

    sleep(1);
    $this->get('/api/form?perPage=15')
        ->assertJsonPath('meta.agegroups.0', ['id' => 1, 'name' => 'Wölfling'])
        ->assertJsonCount(1, 'meta.agegroups');
});

it('testItDoesntDisplayInactiveForms', function () {
    $this->loginNami()->withoutExceptionHandling();

    Form::factory()->isActive(false)->withImage('headerImage', 'lala-2.jpg')->count(1)->create();
    Form::factory()->isActive(true)->withImage('headerImage', 'lala-2.jpg')->count(2)->create();

    sleep(1);
    $this->get('/api/form?perPage=15&filter=' . $this->filterString(['inactive' => true]))->assertJsonCount(3, 'data');
    $this->get('/api/form?perPage=15&filter=' . $this->filterString(['inactive' => false]))->assertJsonCount(2, 'data');
    $this->get('/api/form?perPage=15&filter=' . $this->filterString([]))->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.is_active', true)
        ->assertJsonPath('data.0.is_private', false);
});

it('testItDisplaysDailyForms', function () {
    Carbon::setTestNow(Carbon::parse('2023-03-02'));
    $this->loginNami()->withoutExceptionHandling();
    Form::factory()
        ->withImage('headerImage', 'lala-2.jpg')
        ->from('2023-05-05')
        ->to('2023-05-05')
        ->create();

    sleep(1);
    $this->get('/api/form')
        ->assertJsonPath('data.0.dates', '05.05.2023');
});

it('testItDisplaysPastEvents', function () {
    Carbon::setTestNow(Carbon::parse('2023-05-10'));
    $this->loginNami()->withoutExceptionHandling();
    Form::factory()
        ->withImage('headerImage', 'lala-2.jpg')
        ->from('2023-05-05')
        ->to('2023-05-05')
        ->create();

    sleep(1);
    $this->get('/api/form?filter=' . $this->filterString(['past' => true]))
        ->assertJsonCount(1, 'data');
});

it('testItDisplaysAllForms', function () {
    Carbon::setTestNow(Carbon::parse('2023-03-02'));
    Storage::fake('temp');
    $this->loginNami()->withoutExceptionHandling();
    Form::factory()
        ->withImage('headerImage', 'lala-2.jpg')
        ->from('2023-05-05')
        ->to('2023-06-07')
        ->count(20)
        ->create();

    sleep(1);
    $this->get('/api/form')->assertJsonCount(20, 'data');
});
