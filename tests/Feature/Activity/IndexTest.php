<?php

namespace Tests\Feature\Activity;

use App\Activity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysLocalActivities(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $local = Activity::factory()->name('Local')->create();
        $remote = Activity::factory()->name('Remote')->inNami(123)->create();

        $response = $this->get('/activity');

        $this->assertInertiaHas('Local', $response, 'data.data.0.name');
        $this->assertCount(1, $this->inertia($response, 'data.data'));
    }
}
