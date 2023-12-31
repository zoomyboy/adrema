<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FormIndexActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDisplaysForms(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        Form::factory()->name('lala')->excerpt('fff')->description('desc')->from('2023-05-05')->to('2023-06-07')->mailTop('Guten Tag')->mailBottom('Cheers')->registrationFrom('2023-05-06 04:00:00')->registrationUntil('2023-04-01 05:00:00')->create();

        $this->get(route('form.index'))
            ->assertOk()
            ->assertInertiaPath('data.data.0.name', 'lala')
            ->assertInertiaPath('data.data.0.from_human', '05.05.2023')
            ->assertInertiaPath('data.data.0.to_human', '07.06.2023')
            ->assertInertiaPath('data.data.0.from', '2023-05-05')
            ->assertInertiaPath('data.data.0.to', '2023-06-07')
            ->assertInertiaPath('data.data.0.excerpt', 'fff')
            ->assertInertiaPath('data.data.0.description', 'desc')
            ->assertInertiaPath('data.data.0.mail_top', 'Guten Tag')
            ->assertInertiaPath('data.data.0.mail_bottom', 'Cheers')
            ->assertInertiaPath('data.data.0.registration_from', '2023-05-06 04:00:00')
            ->assertInertiaPath('data.data.0.registration_until', '2023-04-01 05:00:00');
    }
}
