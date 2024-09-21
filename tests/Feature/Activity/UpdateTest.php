<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Activity\Actions\ActivityUpdateAction;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;

covers(ActivityUpdateAction::class);

uses(DatabaseTransactions::class);

it('testItCannotUpdateAnActivityFromNami', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->inNami(67)->name('abc')->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'Lorem',
        'is_filterable' => false,
        'subactivities' => [],
    ]);

    $response->assertSessionHasErrors(['nami_id' => 'Aktivität ist in NaMi. Update des Namens nicht möglich.']);
});

it('testItCanUpdateSubactivitiesOfNamiActivity', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->inNami(67)->name('abc')->create();
    $subactivity = Subactivity::factory()->create();

    $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'abc',
        'is_filterable' => false,
        'subactivities' => [$subactivity->id],
    ]);

    $this->assertDatabaseHas('activity_subactivity', ['activity_id' => $activity->id, 'subactivity_id' => $subactivity->id]);
});

it('testItCannotRemoveANamiSubactivityFromANamiActivity', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->inNami(67)->name('abc')->has(Subactivity::factory()->inNami(69))->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'abc',
        'is_filterable' => false,
        'subactivities' => [],
    ]);

    $response->assertSessionHasErrors(['nami_id' => 'Untertätigkeit kann nicht entfernt werden.']);
});

it('testItCannotAddANamiSubactivityToANamiActivity', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->inNami(67)->name('abc')->create();
    $subactivity = Subactivity::factory()->inNami(60)->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'abc',
        'is_filterable' => false,
        'subactivities' => [$subactivity->id],
    ]);

    $response->assertSessionHasErrors(['nami_id' => 'Untertätigkeit kann nicht hinzugefügt werden.']);
});

it('test it cannot set subactivity to a string', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->create();

    $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'abc',
        'is_filterable' => false,
        'subactivities' => ['AAA'],
    ])->assertSessionHasErrors('subactivities.0');
});


it('testItCannotRemoveANamiSubactivityFromANamiActivityAndSetAnother', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->inNami(67)->name('abc')->has(Subactivity::factory()->inNami(69))->create();
    $otherSubactivity = Subactivity::factory()->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'abc',
        'is_filterable' => false,
        'subactivities' => [$otherSubactivity->id],
    ]);

    $response->assertSessionHasErrors(['nami_id' => 'Untertätigkeit kann nicht entfernt werden.']);
});

it('testNameIsRequired', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => '',
        'is_filterable' => true,
    ]);

    $response->assertSessionHasErrors(['name' => 'Name ist erforderlich.']);
    $response->assertSessionHasErrors(['subactivities' => 'Untergliederungen muss vorhanden sein.']);
});

it('testItUpdatesName', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->name('UUU')->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'Lorem',
        'is_filterable' => true,
        'subactivities' => [],
    ]);

    $response->assertRedirect('/activity');
    $this->assertDatabaseHas('activities', ['name' => 'Lorem', 'is_filterable' => true]);
});

it('testItSetsSubactivities', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->create();
    $subactivity = Subactivity::factory()->create();

    $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'Lorem',
        'is_filterable' => false,
        'subactivities' => [$subactivity->id],
    ]);

    $this->assertDatabaseHas('activity_subactivity', ['activity_id' => $activity->id, 'subactivity_id' => $subactivity->id]);
});

it('testItCannotSetNamiId', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->create();

    $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'Lorem',
        'nami_id' => 66,
        'is_filterable' => false,
        'subactivities' => [],
    ]);

    $this->assertDatabaseHas('activities', ['nami_id' => null]);
});

it('testItUnsetsSubactivities', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()
        ->hasAttached(Subactivity::factory())
        ->create();

    $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'Lorem',
        'is_filterable' => false,
        'subactivities' => [],
    ]);

    $this->assertDatabaseEmpty('activity_subactivity');
});

it('testItCannotSetSubactivityIfItStillHasMembers', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()
        ->hasAttached(Subactivity::factory())
        ->create();
    Member::factory()->defaults()->has(Membership::factory()->for($activity)->for($activity->subactivities->first()))->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'abc',
        'is_filterable' => false,
        'subactivities' => [],
    ]);

    $response->assertSessionHasErrors(['subactivities' => 'Untergliederung hat noch Mitglieder.']);
});

it('test it succeeds when membership is not of removing subactivity', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()
        ->hasAttached(Subactivity::factory())
        ->create();
    Member::factory()->defaults()->has(Membership::factory()->for($activity)->for(Subactivity::factory()))->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'abc',
        'is_filterable' => false,
        'subactivities' => [],
    ]);

    $response->assertSessionDoesntHaveErrors();
});

it('test it succeeds when membership is not of removing activity', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()
        ->hasAttached(Subactivity::factory())
        ->create();
    Member::factory()->defaults()->has(Membership::factory()->for(Activity::factory())->for($activity->subactivities->first()))->create();

    $response = $this->patch(route('activity.update', ['activity' => $activity]), [
        'name' => 'abc',
        'is_filterable' => false,
        'subactivities' => [],
    ]);

    $response->assertSessionDoesntHaveErrors();
});
