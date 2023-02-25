<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    public function testItStoresAnActivity(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $response = $this->post(route('activity.store'), [
            'name' => 'Lorem',
            'subactivities' => [],
        ]);

        $response->assertRedirect('/activity');
        $this->assertDatabaseHas('activities', [
            'name' => 'Lorem',
            'nami_id' => null,
        ]);
        $this->assertDatabaseCount('activity_subactivity', 0);
    }

    public function testNameIsRequired(): void
    {
        $this->login()->loginNami();

        $response = $this->post(route('activity.store'), []);

        $response->assertSessionHasErrors([
            'name' => 'Name ist erforderlich.',
            'subactivities' => 'Untergliederungen muss vorhanden sein.',
        ]);
    }

    public function testNamiIdIsNotSet(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $response = $this->post(route('activity.store'), [
            'name' => 'Lorem',
            'nami_id' => 556,
            'subactivities' => [],
        ]);

        $this->assertDatabaseHas('activities', [
            'nami_id' => null,
        ]);
    }

    public function testItCanStoreASubactivityWithTheActivity(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $subactivity = Subactivity::factory()->create();

        $response = $this->from('/activity')->post(route('activity.store'), [
            'name' => 'Lorem',
            'subactivities' => [$subactivity->id],
        ]);

        $this->assertDatabaseCount('activity_subactivity', 1);
        $this->assertDatabaseHas('activity_subactivity', [
            'activity_id' => Activity::firstWhere('name', 'Lorem')->id,
            'subactivity_id' => $subactivity->id,
        ]);
    }
}
