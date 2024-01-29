<?php

namespace Tests\EndToEnd;

use App\Form\Models\Form;
use Tests\EndToEndTestCase;

class FormIndexTest extends EndToEndTestCase
{
    public function testItHandlesFullTextSearch()
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Form::factory()->to(now()->addYear())->name('ZEM 2024')->create();
        Form::factory()->to(now()->addYear())->name('Rover-Spek 2025')->create();

        sleep(1);
        $this->callFilter('form.index', ['search' => 'ZEM'])
            ->assertInertiaCount('data.data', 1);
        $this->callFilter('form.index', [])
            ->assertInertiaCount('data.data', 2);
    }

    public function testItOrdersByStartDateDesc()
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $form1 = Form::factory()->from(now()->addDays(4))->to(now()->addYear())->create();
        $form2 = Form::factory()->from(now()->addDays(3))->to(now()->addYear())->create();
        $form3 = Form::factory()->from(now()->addDays(2))->to(now()->addYear())->create();

        sleep(1);
        $this->callFilter('form.index', [])
            ->assertInertiaPath('data.data.0.id', $form3->id)
            ->assertInertiaPath('data.data.1.id', $form2->id)
            ->assertInertiaPath('data.data.2.id', $form1->id);
    }

    public function testItShowsPastEvents()
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Form::factory()->count(5)->to(now()->subDays(2))->create();
        Form::factory()->count(3)->to(now()->subDays(5))->create();
        Form::factory()->count(2)->to(now()->addDays(3))->create();

        sleep(1);
        $this->callFilter('form.index', ['past' => true])
            ->assertInertiaCount('data.data', 10);
        $this->callFilter('form.index', [])
            ->assertInertiaCount('data.data', 2);
    }
}
