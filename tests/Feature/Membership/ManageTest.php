<?php

namespace Tests\Feature\Membership;

use App\Activity;
use App\Group;
use App\Member\Data\MembershipData;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia as Assert;

uses(DatabaseTransactions::class);

mutates(MembershipData::class);

it('lists memberships of users', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $activity = Activity::factory()
        ->hasAttached(Subactivity::factory()->name('SubAct'))
        ->name('Act')
        ->create();
    $group = Group::factory()->name('GG')->create();
    $member = Member::factory()->defaults()
        ->for($group)
        ->has(Membership::factory()->for($activity)->for($activity->subactivities->first())->for($group))
        ->male()
        ->name('Max Muster')
        ->create();

    $activity->subactivities()->first();
    $this->callFilter('membership.index', [])
        ->assertInertia(fn(Assert $page) => $page
            ->has('data.data', 1)
            ->has('data.data.0', fn(Assert $page) => $page
                ->where('activity.name', 'Act')
                ->where('subactivity.name', 'SubAct')
                ->where('member.fullname', 'Max Muster')
                ->where('group.name', 'GG')
                ->where('promisedAt', null)
                ->where('links.update', route('membership.update', $member->memberships->first()))
                ->where('links.destroy', route('membership.destroy', $member->memberships->first()))
                ->etc()
            )->has('data.meta', fn (Assert $page) => $page
                ->where('current_page', 1)
                ->where('activities.0.name', 'Act')
                ->where('subactivities.0.name', 'SubAct')
                ->where('groups.1.name', 'GG')
                ->where('filter.active', true)
                ->where('filter.groups', [])
                ->where('filter.activities', [])
                ->where('filter.subactivities', [])
                ->etc()
            )
        );
});

it('lists end date', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $member = Member::factory()->defaults()
        ->has(Membership::factory()->for(Activity::factory())->for(Subactivity::factory())->for(Group::factory())->ended())
        ->male()
        ->name('Max Muster')
        ->create();

    $this->callFilter('membership.index', ['active' => null])
        ->assertInertia(fn(Assert $page) => $page
            ->has('data.data.0', fn(Assert $page) => $page
                ->where('to.human', now()->subDays(2)->format('d.m.Y'))
                ->where('links.update', route('membership.update', $member->memberships->first()))
                ->where('links.destroy', route('membership.destroy', $member->memberships->first()))
                ->etc()
            )->has('data.meta', fn (Assert $page) => $page
                ->where('current_page', 1)
                ->etc()
            )
        );
});

it('filters for active', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    Membership::factory()->defaults()->ended()->create();
    Membership::factory()->defaults()->count(2)->create();

    $this->callFilter('membership.index', [])->assertInertia(fn(Assert $page) => $page->has('data.data', 2));
    $this->callFilter('membership.index', ['active' => null])->assertInertia(fn(Assert $page) => $page->has('data.data', 3));
    $this->callFilter('membership.index', ['active' => false])->assertInertia(fn(Assert $page) => $page->has('data.data', 1));
    $this->callFilter('membership.index', ['active' => true])->assertInertia(fn(Assert $page) => $page->has('data.data', 2));
});

it('filters for group', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $m1 = Membership::factory()->defaults()->count(2)->create();
    $m2 = Membership::factory()->defaults()->create();

    $this->callFilter('membership.index', [])->assertInertia(fn(Assert $page) => $page->has('data', 3));
    $this->callFilter('membership.index', ['groups' => [$m1->first()->group_id]])->assertInertia(fn(Assert $page) => $page->has('data.data', 2)->where('data.meta.filter.groups', [$m1->first()->group_id]));
    $this->callFilter('membership.index', ['groups' => [$m2->group_id]])->assertInertia(fn(Assert $page) => $page->has('data.data', 1)->where('data.meta.filter.groups', [$m2->group_id]));
});

it('filters for activity', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $m1 = Membership::factory()->defaults()->count(2)->create();
    $m2 = Membership::factory()->defaults()->create();

    $this->callFilter('membership.index', [])->assertInertia(fn(Assert $page) => $page->has('data', 3));
    $this->callFilter('membership.index', ['activities' => [$m1->first()->activity_id]])->assertInertia(fn(Assert $page) => $page->has('data.data', 2)->where('data.meta.filter.activities', [$m1->first()->activity_id]));
    $this->callFilter('membership.index', ['activities' => [$m2->activity_id]])->assertInertia(fn(Assert $page) => $page->has('data.data', 1)->where('data.meta.filter.activities', [$m2->activity_id]));
});

it('filters for subactivity', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $m1 = Membership::factory()->defaults()->count(2)->create();
    $m2 = Membership::factory()->defaults()->create();

    $this->callFilter('membership.index', [])->assertInertia(fn(Assert $page) => $page->has('data', 3));
    $this->callFilter('membership.index', ['subactivities' => [$m1->first()->subactivity_id]])->assertInertia(fn(Assert $page) => $page->has('data.data', 2)->where('data.meta.filter.subactivities', [$m1->first()->subactivity_id]));
    $this->callFilter('membership.index', ['subactivities' => [$m2->subactivity_id]])->assertInertia(fn(Assert $page) => $page->has('data.data', 1)->where('data.meta.filter.subactivities', [$m2->subactivity_id]));
});
