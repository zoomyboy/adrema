<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Group;
use App\Member\Member;
use App\Member\Membership;
use App\Payment\Payment;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\Child;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItGetsMembers(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        Member::factory()->defaults()->for($group)->create([
            'firstname' => '::firstname::',
            'address' => 'Kölner Str 3',
            'zip' => 33333,
            'location' => 'Hilden',
        ]);

        $response = $this->get('/member');

        $this->assertComponent('member/VIndex', $response);
        $this->assertInertiaHas('::firstname::', $response, 'data.data.0.firstname');
        $this->assertInertiaHas('Kölner Str 3, 33333 Hilden', $response, 'data.data.0.full_address');
        $this->assertInertiaHas($group->id, $response, 'data.data.0.group_id');
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
        Member::factory()
            ->defaults()
            ->create(['lastname' => 'C']);

        $response = $this->get('/member');

        $this->assertInertiaHas(url("/member/{$member->id}/efz"), $response, 'data.data.0.efz_link');
        $this->assertInertiaHas(null, $response, 'data.data.1.efz_link');
        $this->assertInertiaHas(null, $response, 'data.data.2.efz_link');
        $this->assertInertiaHas(true, $response, 'data.data.0.is_leader');
        $this->assertInertiaHas(false, $response, 'data.data.1.is_leader');
        $this->assertInertiaHas(false, $response, 'data.data.2.is_leader');
    }

    public function testItShowsAgeGroupIcon(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ Mitglied', 123, 'Wölfling', 12))
            ->create();

        $response = $this->get('/member');

        $this->assertInertiaHas('woelfling', $response, 'data.data.0.age_group_icon');
    }

    public function testItShowsActivitiesAndSubactivities(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('Biber'))->name('€ Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        $response = $this->get('/member');

        $this->assertInertiaHas('Biber', $response, "subactivities.{$activity->id}.{$subactivity->id}");
        $this->assertInertiaHas('Biber', $response, "filterSubactivities.{$subactivity->id}");
        $this->assertInertiaHas('€ Mitglied', $response, "activities.{$activity->id}");
    }

    public function testItShowsActivityAndSubactivityNamesOfMember(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->for($group)->in('€ Mitglied', 122, 'Wölfling', 234)->from('2022-11-02'))
            ->create();

        $response = $this->get('/member');

        $this->assertInertiaHas([
            'activity_id' => $member->memberships->first()->activity_id,
            'subactivity_id' => $member->memberships->first()->subactivity_id,
            'activity_name' => '€ Mitglied',
            'subactivity_name' => 'Wölfling',
            'human_date' => '02.11.2022',
            'group_id' => $group->id,
            'id' => $member->memberships->first()->id,
        ], $response, 'data.data.0.memberships.0');
    }

    public function testItDoesntShowEndedMemberships(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->for($group)->in('€ Mitglied', 122, 'Wölfling', 234)->ended())
            ->create();

        $response = $this->get('/member');

        $this->assertCount(0, $this->inertia($response, 'data.data.0.memberships'));
    }

    public function testItReturnsPayments(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->has(Payment::factory()->notPaid()->nr('2019')->subscription('Free', [
                new Child('a', 1000),
                new Child('b', 50),
            ]))
            ->defaults()->create();

        $response = $this->get('/member');

        $this->assertInertiaHas([
            'subscription' => [
                'name' => 'Free',
                'id' => $member->payments->first()->subscription->id,
                'amount' => 1050,
            ],
            'subscription_id' => $member->payments->first()->subscription->id,
            'status_name' => 'Nicht bezahlt',
            'nr' => '2019',
         ], $response, 'data.data.0.payments.0');
        $this->assertInertiaHas([
            'id' => $member->subscription->id,
            'name' => $member->subscription->name,
         ], $response, 'data.data.0.subscription');
    }

    public function testItCanFilterForBillKinds(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->emailBillKind()->create();
        Member::factory()->defaults()->postBillKind()->create();
        Member::factory()->defaults()->postBillKind()->create();

        $emailResponse = $this->callFilter('member.index', ['bill_kind' => 'E-Mail']);
        $postResponse = $this->callFilter('member.index', ['bill_kind' => 'Post']);

        $this->assertCount(1, $this->inertia($emailResponse, 'data.data'));
        $this->assertCount(2, $this->inertia($postResponse, 'data.data'));
        $this->assertInertiaHas('E-Mail', $emailResponse, 'data.meta.filter.bill_kind');
    }

    public function testItFiltersForAusstand(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()
            ->has(Payment::factory()->notPaid()->subscription('Free', [new Child('b', 50)]))
            ->defaults()->create();
        Member::factory()->defaults()->create();
        Member::factory()->defaults()->create();

        $defaultResponse = $this->callFilter('member.index', []);
        $ausstandResponse = $this->callFilter('member.index', ['ausstand' => true]);

        $this->assertCount(3, $this->inertia($defaultResponse, 'data.data'));
        $this->assertCount(1, $this->inertia($ausstandResponse, 'data.data'));
        $this->assertInertiaHas(true, $ausstandResponse, 'data.meta.filter.ausstand');
        $this->assertInertiaHas(false, $defaultResponse, 'data.meta.filter.ausstand');
    }

    public function testItHandlesFullTextSearch(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        $searchResponse = $this->callFilter('member.index', ['search' => 'Maxö']);

        $this->assertInertiaHas('Maxö', $searchResponse, 'data.meta.filter.search');
    }

    public function testItLoadsGroups(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Group::factory()->name('UUI')->create();

        $response = $this->get('/member');

        $this->assertInertiaHas('UUI', $response, 'data.meta.groups.1.name');
    }
}
