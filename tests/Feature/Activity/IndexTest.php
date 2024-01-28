<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysActivities(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $first = Activity::factory()->name('Local')->create();
        Activity::factory()->name('Remote')->inNami(123)->create();

        $response = $this->get('/activity');

        $this->assertInertiaHas('Local', $response, 'data.data.0.name');
        $this->assertInertiaHas(route('activity.update', ['activity' => $first]), $response, 'data.data.0.links.update');
        $this->assertInertiaHas(route('activity.destroy', ['activity' => $first]), $response, 'data.data.0.links.destroy');
        $this->assertInertiaHas(route('membership.masslist.index'), $response, 'data.meta.links.membership_masslist');
        $this->assertCount(2, $this->inertia($response, 'data.data'));
    }
}
