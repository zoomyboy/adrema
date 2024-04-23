<?php

namespace Tests\EndToEnd\Form;

use App\Form\Models\Form;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\EndToEndTestCase;
use Tests\Feature\Form\FormtemplateSectionRequest;

class FormApiListActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItDisplaysForms(): void
    {
        Carbon::setTestNow(Carbon::parse('2023-03-02'));
        Storage::fake('temp');
        $this->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->name('lala 2')
            ->excerpt('fff')
            ->withImage('headerImage', 'lala-2.jpg')
            ->description('desc')
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
            ->assertJsonPath('data.0.description', 'desc')
            ->assertJsonPath('data.0.slug', 'lala-2')
            ->assertJsonPath('data.0.image', $form->getMedia('headerImage')->first()->getFullUrl('square'))
            ->assertJsonPath('data.0.dates', '05.05.2023 - 07.06.2023')
            ->assertJsonPath('data.0.from_human', '05.05.2023')
            ->assertJsonPath('data.0.to_human', '07.06.2023')
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.base_url', url(''))
            ->assertJsonPath('meta.total', 1);
    }

    public function testItDisplaysDefaultValueOfField(): void
    {
        Storage::fake('temp');
        $this->loginNami()->withoutExceptionHandling();
        Form::factory()->withImage('headerImage', 'lala-2.jpg')
            ->sections([FormtemplateSectionRequest::new()->fields([$this->textField()])])
            ->create();

        sleep(1);
        $this->get('/api/form?perPage=15')->assertJsonPath('data.0.config.sections.0.fields.0.value', null);
    }

    public function testItDisplaysRemoteGroups(): void
    {
        $this->loginNami()->withoutExceptionHandling();
        Subactivity::factory()->inNami(1)->name('Wölfling')->ageGroup(true)->create();
        Subactivity::factory()->inNami(50)->name('Biber')->ageGroup(false)->create();
        Subactivity::factory()->name('Lager')->ageGroup(true)->create();

        sleep(1);
        $this->get('/api/form?perPage=15')
            ->assertJsonPath('meta.agegroups.0', ['id' => 1, 'name' => 'Wölfling'])
            ->assertJsonCount(1, 'meta.agegroups');
    }

    public function testItDisplaysDailyForms(): void
    {
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
    }

    public function testItDisplaysPastEvents(): void
    {
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
    }
}
