<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubactivityShowTest extends TestCase
{
    use DatabaseTransactions;

    public function testItShowsASubactivity(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $subactivity = Subactivity::factory()->name('Asas')->filterable()->hasAttached(Activity::factory())->create();

        $response = $this->getJson(route('api.subactivity.show', ['subactivity' => $subactivity]));

        $response->assertJsonPath('data.id', $subactivity->id);
        $response->assertJsonPath('data.name', $subactivity->name);
        $response->assertJsonPath('data.links.update', route('api.subactivity.update', ['subactivity' => $subactivity]));
        $response->assertJsonPath('data.links.show', route('api.subactivity.show', ['subactivity' => $subactivity]));
        $response->assertJsonPath('data.activities.0', $subactivity->activities->first()->id);
        $response->assertJsonPath('meta.activities.0.name', $subactivity->activities->first()->name);
        $response->assertJsonPath('meta.activities.0.id', $subactivity->activities->first()->id);
    }
}
