<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

it('testItEditsAnActivity', function () {
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
    $this->assertInertiaHas([
        'id' => $activity->subactivities->first()->id,
        'name' => 'Pupu',
        'is_filterable' => true,
        'links' => [
            'show' => route('api.subactivity.show', ['subactivity' => $activity->subactivities->first()->id]),
            'update' => route('api.subactivity.update', ['subactivity' => $activity->subactivities->first()->id]),
        ],
    ], $response, 'meta.subactivities.0');
});
