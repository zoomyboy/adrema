<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EditTest extends TestCase
{
    use DatabaseTransactions;

    public function testItEditsAnActivity(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $activity = Activity::factory()->name('Asas')->hasAttached(Subactivity::factory()->name('Pupu'))->create();

        $response = $this->get(route('activity.edit', ['activity' => $activity]));

        $this->assertInertiaHas([
            'name' => 'Asas',
            'is_filterable' => false,
            'subactivities' => [$activity->subactivities->first()->id],
        ], $response, 'data');
        $this->assertInertiaHas([
            'id' => $activity->subactivities->first()->id,
            'name' => 'Pupu',
        ], $response, 'meta.subactivities.0');
    }
}
