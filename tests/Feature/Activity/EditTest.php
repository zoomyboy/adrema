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
        $activity = Activity::factory()->name('Asas')->hasAttached(Subactivity::factory()->name('Pupu')->filterable())->create();

        $response = $this->get(route('activity.edit', ['activity' => $activity]));

        $this->assertInertiaHas([
            'name' => 'Asas',
            'is_filterable' => false,
            'subactivities' => [$activity->subactivities->first()->id],
            'subactivity_model' => [
                'activities' => [$activity->id],
                'is_age_group' => false,
                'is_filterable' => false,
                'name' => '',
            ],
        ], $response, 'data');
        $this->assertInertiaHas([
            'id' => $activity->subactivities->first()->id,
            'name' => 'Pupu',
            'is_filterable' => true,
        ], $response, 'meta.subactivities.0');
    }
}
