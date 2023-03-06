<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Member\Member;
use App\Member\Membership;
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

    public function testItCannotUpdateNameIfInNami(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(123)->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->name('abc')->inNami(777)->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'aaaa',
            'nami_id' => 777,
            'activities' => [$activity->id],
            'is_filterable' => false,
        ]);

        $response->assertJsonValidationErrors(['name' => 'Untertätigkeit ist in NaMi. Update des Namens nicht möglich.']);
    }

    public function testItCannotUpdateNamiId(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(123)->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->name('abc')->inNami(777)->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'abc',
            'nami_id' => 556,
            'activities' => [$activity->id],
            'is_filterable' => false,
        ]);

        $this->assertDatabaseHas('subactivities', [
            'id' => $subactivity->id,
            'nami_id' => 777,
        ]);
    }

    public function testItCannotSetNamiId(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(123)->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->name('abc')->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'abc',
            'nami_id' => 556,
            'activities' => [$activity->id],
            'is_filterable' => false,
        ]);

        $this->assertDatabaseHas('subactivities', [
            'id' => $subactivity->id,
            'nami_id' => null,
        ]);
    }

    public function testItCanUpdateIsFilterableIfInNami(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(123)->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->name('abc')->filterable()->inNami(777)->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'abc',
            'activities' => [$activity->id],
            'is_filterable' => false,
        ]);

        $this->assertDatabaseHas('subactivities', [
            'id' => $subactivity->id,
            'is_filterable' => false,
        ]);
    }

    public function testItCanUpdateNameWhenNotInNami(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(123)->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->name('abc')->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'def',
            'activities' => [$activity->id],
            'is_filterable' => false,
        ]);

        $this->assertDatabaseHas('subactivities', [
            'id' => $subactivity->id,
            'name' => 'def',
        ]);
    }

    public function testNameShouldBeUnique(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(123)->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->name('abc')->create();
        Subactivity::factory()->hasAttached($activity)->name('def')->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'def',
            'activities' => [$activity->id],
            'is_filterable' => false,
        ]);

        $response->assertJsonValidationErrors(['name' => 'Name ist bereits vergeben']);
    }

    public function testItCanSetAnotherActivity(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();
        $newActivity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'abc',
            'activities' => [$activity->id, $newActivity->id],
            'is_filterable' => false,
        ]);

        $this->assertDatabaseHas('activity_subactivity', ['activity_id' => $activity->id, 'subactivity_id' => $subactivity->id]);
        $this->assertDatabaseHas('activity_subactivity', ['activity_id' => $newActivity->id, 'subactivity_id' => $subactivity->id]);
    }

    public function testItCannotSetAnotherNamiActivity(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();
        $newActivity = Activity::factory()->inNami(556)->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->name('abc')->inNami(667)->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'abc',
            'activities' => [$activity->id, $newActivity->id],
            'is_filterable' => false,
        ]);

        $response->assertJsonValidationErrors(['activities' => 'Tätigkeit kann nicht hinzugefügt werden.']);
    }

    public function testItCannotRemoveANamiActivity(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();
        $newActivity = Activity::factory()->inNami(556)->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->hasAttached($newActivity)->name('abc')->inNami(667)->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'abc',
            'activities' => [$activity->id],
            'is_filterable' => false,
        ]);

        $response->assertJsonValidationErrors(['activities' => 'Tätigkeit kann nicht entfernt werden.']);
    }

    public function testItCannotRemoveActivityIfMembershipsHasMembers(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();
        $newActivity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->create();
        Member::factory()->defaults()->has(Membership::factory()->for($activity)->for($subactivity))->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'abc',
            'activities' => [$newActivity->id],
            'is_filterable' => false,
        ]);

        $response->assertJsonValidationErrors(['activities' => 'Tätigkeit hat noch Mitglieder.']);
    }

    public function testItCannotSetNoActivity(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->hasAttached($activity)->create();

        $response = $this->patchJson(route('api.subactivity.update', ['subactivity' => $subactivity->id]), [
            'name' => 'abc',
            'activities' => [],
            'is_filterable' => false,
        ]);

        $response->assertJsonValidationErrors(['activities' => 'Tätigkeiten muss mindestens 1 Elemente haben.']);
    }
}
