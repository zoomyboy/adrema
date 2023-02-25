<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysLocalActivities(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $first = Activity::factory()->name('Local')->create();
        Activity::factory()->name('Remote')->inNami(123)->create();

        $response = $this->get('/activity');

        $this->assertInertiaHas('Local', $response, 'data.data.0.name');
        $this->assertInertiaHas(route('activity.update', ['activity' => $first]), $response, 'data.data.0.links.update');
        $this->assertInertiaHas(route('activity.destroy', ['activity' => $first]), $response, 'data.data.0.links.destroy');
        $this->assertCount(1, $this->inertia($response, 'data.data'));
    }

    public function testItDisplaysDefaultFilter(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $response = $this->callFilter('activity.index', []);

        $this->assertInertiaHas(null, $response, 'data.meta.filter.subactivity');
    }

    public function testItFiltersActivityBySubactivity(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $subactivity = Subactivity::factory()->name('jjon')->create();
        Activity::factory()->name('Local')->hasAttached($subactivity)->create();
        Activity::factory()->count(2)->name('Local')->create();

        $response = $this->callFilter('activity.index', ['subactivity_id' => $subactivity->id]);

        $this->assertInertiaHas($subactivity->id, $response, 'data.meta.filter.subactivity_id');
        $this->assertCount(1, $this->inertia($response, 'data.data'));
    }
}
