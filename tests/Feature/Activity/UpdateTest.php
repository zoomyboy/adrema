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
        $activity = Activity::factory()->inNami(67)->name('abc')->create();

        $response = $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'Lorem',
            'subactivities' => [],
        ]);

        $response->assertSessionHasErrors(['nami_id' => 'Aktivität ist in NaMi. Update des Namens nicht möglich.']);
    }

    public function testItCanUpdateSubactivitiesOfNamiActivity(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(67)->name('abc')->create();
        $subactivity = Subactivity::factory()->create();

        $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'abc',
            'subactivities' => [$subactivity->id],
        ]);

        $this->assertDatabaseHas('activity_subactivity', ['activity_id' => $activity->id, 'subactivity_id' => $subactivity->id]);
    }

    public function testItCannotRemoveANamiSubactivityFromANamiActivity(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(67)->name('abc')->has(Subactivity::factory()->inNami(69))->create();

        $response = $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'abc',
            'subactivities' => [],
        ]);

        $response->assertSessionHasErrors(['nami_id' => 'Untertätigkeit kann nicht entfernt werden.']);
    }

    public function testItCannotAddANamiSubactivityToANamiActivity(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(67)->name('abc')->create();
        $subactivity = Subactivity::factory()->inNami(60)->create();

        $response = $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'abc',
            'subactivities' => [$subactivity->id],
        ]);

        $response->assertSessionHasErrors(['nami_id' => 'Untertätigkeit kann nicht hinzugefügt werden.']);
    }

    public function testItCannotRemoveANamiSubactivityFromANamiActivityAndSetAnother(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(67)->name('abc')->has(Subactivity::factory()->inNami(69))->create();
        $otherSubactivity = Subactivity::factory()->create();

        $response = $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'abc',
            'subactivities' => [$otherSubactivity->id],
        ]);

        $response->assertSessionHasErrors(['nami_id' => 'Untertätigkeit kann nicht entfernt werden.']);
    }

    public function testNameIsRequired(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();

        $response = $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name' => 'Name ist erforderlich.']);
        $response->assertSessionHasErrors(['subactivities' => 'Untergliederungen muss vorhanden sein.']);
    }

    public function testItUpdatesName(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->name('UUU')->create();

        $response = $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'Lorem',
            'subactivities' => [],
        ]);

        $response->assertRedirect('/activity');
        $this->assertDatabaseHas('activities', ['name' => 'Lorem']);
    }

    public function testItSetsSubactivities(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->create();

        $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'Lorem',
            'subactivities' => [$subactivity->id],
        ]);

        $this->assertDatabaseHas('activity_subactivity', ['activity_id' => $activity->id, 'subactivity_id' => $subactivity->id]);
    }

    public function testItCannotSetNamiId(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();

        $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'Lorem',
            'nami_id' => 66,
            'subactivities' => [],
        ]);

        $this->assertDatabaseHas('activities', ['nami_id' => null]);
    }

    public function testItUnsetsSubactivities(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()
            ->hasAttached(Subactivity::factory())
            ->create();

        $this->patch(route('activity.update', ['activity' => $activity]), [
            'name' => 'Lorem',
            'subactivities' => [],
        ]);

        $this->assertDatabaseEmpty('activity_subactivity');
    }

}
