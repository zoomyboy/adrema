<?php

namespace Tests\Feature\Form;

use App\Form\Models\Formtemplate;
use App\Lib\Events\Succeeded;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FormtemplateDestroyActionTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDestroysAFormtemplate(): void
    {
        Event::fake([Succeeded::class]);
        $this->login()->loginNami()->withoutExceptionHandling();
        $formtemplate = Formtemplate::factory()->create();

        $this->deleteJson(route('formtemplate.destroy', ['formtemplate' => $formtemplate]))
            ->assertOk();

        $this->assertDatabaseCount('formtemplates', 0);
        Event::assertDispatched(Succeeded::class, fn (Succeeded $event) => $event->message === 'Vorlage gelöscht.');
    }
}
