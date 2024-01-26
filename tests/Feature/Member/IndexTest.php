<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Group;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
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
        $member = Member::factory()->defaults()->for($group)
            ->create([
                'firstname' => '::firstname::',
                'address' => 'Kölner Str 3',
                'zip' => 33333,
                'location' => 'Hilden',
            ]);

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

        $response = $this->get('/member');

        $this->assertInertiaHas('woelfling', $response, 'data.data.0.age_group_icon');
    }

    public function testAgeIsNullWhenBirthdayIsNull(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->create(['birthday' => null]);

        $response = $this->get('/member');

        $this->assertInertiaHas(null, $response, 'data.data.0.age');
        $this->assertInertiaHas(null, $response, 'data.data.0.birthday');
    }

    public function testItShowsActivitiesAndSubactivities(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('Biber'))->name('€ Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        $response = $this->get('/member');

        $this->assertInertiaHas('Biber', $response, "data.meta.formSubactivities.{$activity->id}.{$subactivity->id}");
        $this->assertInertiaHas('Biber', $response, "data.meta.filterSubactivities.{$subactivity->id}");
        $this->assertInertiaHas('€ Mitglied', $response, "data.meta.formActivities.{$activity->id}");
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

    public function testItCanFilterForGroups(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();
        Member::factory()->defaults()->for($group1)->create();
        Member::factory()->defaults()->for($group1)->create();
        Member::factory()->defaults()->for($group1)->create();

        $oneResponse = $this->callFilter('member.index', ['group_ids' => [$group1->id]]);
        $twoResponse = $this->callFilter('member.index', ['group_ids' => [$group2->id]]);

        $this->assertCount(3, $this->inertia($oneResponse, 'data.data'));
        $this->assertCount(0, $this->inertia($twoResponse, 'data.data'));
        $this->assertInertiaHas([$group1->id], $oneResponse, 'data.meta.filter.group_ids');
    }

    public function testItFiltersForAusstand(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()
            ->has(InvoicePosition::factory()->for(Invoice::factory()->status(InvoiceStatus::NEW)))
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

    public function testItLoadsGroups(): void
    {
        $parent1 = Group::factory()->name('par1')->create();
        $child1 = Group::factory()->name('ch1')->for($parent1, 'parent')->create();
        $child2 = Group::factory()->name('ch2')->for($parent1, 'parent')->create();
        $parent2 = Group::factory()->name('par2')->create();
        $this->withoutExceptionHandling()->login()->loginNami(12345, 'password', $parent1);

        $response = $this->get('/member');

        $this->assertInertiaHas('par1', $response, 'data.meta.groups.0.name');
        $this->assertInertiaHas('- ch1', $response, 'data.meta.groups.1.name');
        $this->assertInertiaHas('- ch2', $response, 'data.meta.groups.2.name');
        $this->assertInertiaHas('par2', $response, 'data.meta.groups.3.name');
    }
}
