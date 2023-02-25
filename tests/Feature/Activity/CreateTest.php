<?php

namespace Tests\Feature\Activity;

use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCreatesAnActivity(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        Subactivity::factory()->name('Pupu')->create();

        $response = $this->get(route('activity.create'));

        $this->assertInertiaHas([
            'name' => '',
            'subactivities' => [],
        ], $response, 'data');
        $this->assertInertiaHas([
            'id' => Subactivity::first()->id,
            'name' => 'Pupu',
        ], $response, 'meta.subactivities.0');
    }

}
