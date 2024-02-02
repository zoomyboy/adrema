<?php

namespace Tests\EndToEnd\Member;

use App\Activity;
use App\Group;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\EndToEndTestCase;

class IndexTest extends EndToEndTestCase
{
    public function testItGetsMembers(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        $member = Member::factory()->defaults()->for($group)
            ->create([
                'firstname' => '::firstname::',
                'address' => 'Kölner Str 3',
                'zip' => 33333,
                'location' => 'Hilden',
            ]);

        sleep(1);
        $response = $this->get('/member');

        $this->assertComponent('member/VIndex', $response);
        $this->assertInertiaHas('::firstname::', $response, 'data.data.0.firstname');
        $this->assertInertiaHas(false, $response, 'data.data.0.has_nami');
        $this->assertInertiaHas('Kölner Str 3, 33333 Hilden', $response, 'data.data.0.full_address');
        $this->assertInertiaHas($group->id, $response, 'data.data.0.group_id');
        $this->assertInertiaHas(null, $response, 'data.data.0.memberships');
        $this->assertInertiaHas(url("/member/{$member->id}/membership"), $response, 'data.data.0.links.membership_index');
        $this->assertInertiaHas(url("/member/{$member->id}/invoice-position"), $response, 'data.data.0.links.invoiceposition_index');
        $this->assertInertiaHas(url("/member/{$member->id}/course"), $response, 'data.data.0.links.course_index');
        $this->assertInertiaHas([
            'id' => $member->subscription->id,
            'name' => $member->subscription->name,
        ], $response, 'data.data.0.subscription');
    }

    public function testFieldsCanBeNull(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        Member::factory()->defaults()->for($group)->create([
            'birthday' => null,
            'address' => null,
            'zip' => null,
            'location' => null,
        ]);

        sleep(1);
        $response = $this->get('/member');

        $this->assertInertiaHas(null, $response, 'data.data.0.birthday');
        $this->assertInertiaHas(null, $response, 'data.data.0.address');
        $this->assertInertiaHas(null, $response, 'data.data.0.zip');
        $this->assertInertiaHas(null, $response, 'data.data.0.location');
    }

    public function testItShowsEfzForEfzMembership(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 455, 'Pfadfinder', 15))
            ->create(['lastname' => 'A']);
        Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ Mitglied', 456, 'Pfadfinder', 16))
            ->create(['lastname' => 'B']);
        $emptyMember = Member::factory()
            ->defaults()
            ->create(['lastname' => 'C']);

        sleep(1);
        $response = $this->get('/member');

        $this->assertInertiaHas(url("/member/{$member->id}/efz"), $response, 'data.data.0.efz_link');
        $this->assertInertiaHas(url("/member/{$emptyMember->id}/efz"), $response, 'data.data.2.efz_link');
        $this->assertInertiaHas(true, $response, 'data.data.0.is_leader');
        $this->assertInertiaHas(false, $response, 'data.data.1.is_leader');
        $this->assertInertiaHas(false, $response, 'data.data.2.is_leader');
    }

    public function testItHasNoEfzLinkWhenAddressIsMissing(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 455, 'Pfadfinder', 15))
            ->create(['address' => null]);

        $response = $this->get('/member');

        $this->assertInertiaHas(null, $response, 'data.data.0.efz_link');
    }

    public function testItShowsAgeGroupIcon(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ Mitglied', 123, 'Wölfling', 12))
            ->create();

        sleep(1);
        $response = $this->get('/member');

        $this->assertInertiaHas('woelfling', $response, 'data.data.0.age_group_icon');
    }

    public function testAgeIsNullWhenBirthdayIsNull(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->create(['birthday' => null]);

        $response = $this->get('/member');

        $this->assertInertiaHas(null, $response, 'data.data.0.age');
        $this->assertInertiaHas(null, $response, 'data.data.0.birthday');
    }

    public function testItShowsActivitiesAndSubactivities(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('Biber'))->name('€ Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        sleep(1);
        $this->get('/member')
            ->assertInertiaPath("data.meta.formSubactivities.{$activity->id}.{$subactivity->id}", 'Biber')
            ->assertInertiaPath("data.meta.filterSubactivities.{$subactivity->id}", 'Biber')
            ->assertInertiaPath("data.meta.formActivities.{$activity->id}", '€ Mitglied');
    }

    public function testItCanFilterForBillKinds(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->emailBillKind()->create();
        Member::factory()->defaults()->postBillKind()->create();
        Member::factory()->defaults()->postBillKind()->create();

        sleep(1);
        $this->callFilter('member.index', ['bill_kind' => 'E-Mail'])
            ->assertInertiaCount('data.data', 1)
            ->assertInertiaPath('data.meta.filter.bill_kind', 'E-Mail');
        $this->callFilter('member.index', ['bill_kind' => 'Post'])
            ->assertInertiaCount('data.data', 2);
    }

    public function testItCanFilterForGroups(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        Member::factory()->defaults()->for($group1)->create();
        Member::factory()->defaults()->for($group1)->create();
        Member::factory()->defaults()->for($group1)->create();

        sleep(1);
        $oneResponse = $this->callFilter('member.index', ['group_ids' => [$group1->id]]);
        $twoResponse = $this->callFilter('member.index', ['group_ids' => [$group2->id]]);

        $this->assertCount(3, $this->inertia($oneResponse, 'data.data'));
        $this->assertCount(0, $this->inertia($twoResponse, 'data.data'));
        $this->assertInertiaHas([$group1->id], $oneResponse, 'data.meta.filter.group_ids');
    }

    public function testItLoadsGroups(): void
    {
        $this->withoutExceptionHandling();
        $parent1 = Group::factory()->name('par1')->create();
        $child1 = Group::factory()->name('ch1')->for($parent1, 'parent')->create();
        $child2 = Group::factory()->name('ch2')->for($parent1, 'parent')->create();
        $parent2 = Group::factory()->name('par2')->create();
        $this->withoutExceptionHandling()->login()->loginNami(12345, 'password', $parent1);

        sleep(1);
        $response = $this->get('/member');
        $response->assertOk();

        $this->assertInertiaHas('par1', $response, 'data.meta.groups.0.name');
        $this->assertInertiaHas('- ch1', $response, 'data.meta.groups.1.name');
        $this->assertInertiaHas('- ch2', $response, 'data.meta.groups.2.name');
        $this->assertInertiaHas('par2', $response, 'data.meta.groups.3.name');
    }
}
