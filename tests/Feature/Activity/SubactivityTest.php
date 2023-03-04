<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubactivityTest extends TestCase
{
    use DatabaseTransactions;

    public function testItStoresASubactivity(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $activity = Activity::factory()->create();

        $response = $this->postJson(route('api.subactivity.store'), [
            'name' => 'Leiter Lost',
            'activities' => [$activity->id],
            'is_filterable' => true,
        ]);

        $subactivity = Subactivity::where('name', 'Leiter Lost')->firstOrFail();
        $response->assertJsonPath('id', $subactivity->id);

        $this->assertDatabaseHas('subactivities', [
            'name' => 'Leiter Lost',
            'nami_id' => null,
            'is_age_group' => 0,
            'is_filterable' => 1,
        ]);
        $this->assertDatabaseHas('activity_subactivity', ['activity_id' => $activity->id, 'subactivity_id' => $subactivity->id]);
    }

    public function testNameIsRequired(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();

        $response = $this->postJson(route('api.subactivity.store'), [
            'name' => '',
            'activities' => [$activity->id],
            'is_filterable' => true,
        ]);
        $response->assertJsonValidationErrors(['name' => 'Name ist erforderlich.']);
    }

    public function testNameIsUnique(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->name('Lott')->create();

        $response = $this->postJson(route('api.subactivity.store'), [
            'name' => 'Lott',
            'activities' => [$activity->id],
            'is_filterable' => true,
        ]);
        $response->assertJsonValidationErrors(['name' => 'Name ist bereits vergeben.']);
    }

    public function testItNeedsAtLeasttOneActivity(): void
    {
        $this->login()->loginNami();

        $response = $this->postJson(route('api.subactivity.store'), [
            'name' => '',
            'activities' => [],
            'is_filterable' => true,
        ]);
        $response->assertJsonValidationErrors(['activities' => 'Tätigkeiten muss mindestens 1 Elemente haben.']);
    }

    public function testNamiIdIsNotSet(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();

        $this->postJson(route('api.subactivity.store'), [
            'name' => 'aaaa',
            'nami_id' => 556,
            'activities' => [$activity->id],
            'is_filterable' => true,
        ]);
        $this->assertDatabaseMissing('subactivities', ['nami_id' => 556]);
    }
}
