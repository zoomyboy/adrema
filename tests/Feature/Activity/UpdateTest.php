<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCannotUpdateAnActivityFromNami(): void
    {
        $this->login()->loginNami();

        $activity = Activity::factory()->inNami(67)->create();

        $response = $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'Lorem',
            'subactivities' => [],
        ]);

        $response->assertStatus(403);
    }

    public function testItUpdatesName(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $activity = Activity::factory()->name('before')->create();

        $response = $this->from("/activity/{$activity->id}")->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'after',
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
        $activity = Activity::factory()->name('before')->create();
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
