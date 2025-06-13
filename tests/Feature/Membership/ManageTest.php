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
            ->has('data.0', fn(Assert $page) => $page
                ->where('activity.name', 'Act')
                ->where('subactivity.name', 'SubAct')
                ->where('group.name', 'GG')
                ->where('promisedAt', null)
                ->where('links.update', route('membership.update', $member->memberships->first()))
                ->where('links.destroy', route('membership.destroy', $member->memberships->first()))
                ->etc()
            )->has('meta', fn (Assert $page) => $page
                ->where('current_page', 1)
                ->etc()
            )
        );
});

