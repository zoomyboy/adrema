<?php

namespace Tests\EndToEnd;

use App\Activity;
use App\Country;
use App\Group;
use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Tests\EndToEndTestCase;

class MemberIndexTest extends EndToEndTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        Country::factory()->create(['name' => 'Deutschland']);
        $this->withoutExceptionHandling()->login()->loginNami();
    }

    public function testItHandlesFullTextSearch(): void
    {
        Member::factory()->defaults()->count(2)->create(['firstname' => 'Alexander']);
        Member::factory()->defaults()->create(['firstname' => 'Heinrich']);

        sleep(1);
        $this->callFilter('member.index', ['search' => 'Alexander'])
            ->assertInertiaCount('data.data', 2);
        $this->callFilter('member.index', ['search' => 'Heinrich'])
            ->assertInertiaCount('data.data', 1);
    }

    public function testItGetsDefaultCountryFromDefaultModel(): void
    {
        $this->callFilter('member.index', [])->assertInertiaPath('data.meta.default.country_id', Country::firstWhere('name', 'Deutschland')->id);
    }

    public function testItHandlesAddress(): void
    {
        Member::factory()->defaults()->create(['address' => '']);
        Member::factory()->defaults()->create(['zip' => '']);
        Member::factory()->defaults()->create(['location' => '']);
        Member::factory()->defaults()->create();

        sleep(1);
        $this->callFilter('member.index', ['has_full_address' => false])
            ->assertInertiaCount('data.data', 3)
            ->assertInertiaPath('data.meta.total', 3)
            ->assertInertiaPath('data.meta.from', 1)
            ->assertInertiaPath('data.meta.to', 3);
        $this->callFilter('member.index', ['has_full_address' => true])
            ->assertInertiaCount('data.data', 1)
            ->assertInertiaPath('data.meta.total', 1)
            ->assertInertiaPath('data.meta.from', 1)
            ->assertInertiaPath('data.meta.to', 1);
    }

    public function testItHandlesBirthday(): void
    {
        Member::factory()->defaults()->create(['birthday' => null]);
        Member::factory()->defaults()->count(2)->create();

        sleep(1);
        $this->callFilter('member.index', ['has_birthday' => true])
            ->assertInertiaCount('data.data', 2);
        $this->callFilter('member.index', ['has_birthday' => false])
            ->assertInertiaCount('data.data', 1);
    }

    public function testItHandlesBillKind(): void
    {
        Member::factory()->defaults()->postBillKind()->create();
        Member::factory()->defaults()->emailBillKind()->count(2)->create();

        sleep(1);
        $this->callFilter('member.index', ['bill_kind' => BillKind::POST->value])
            ->assertInertiaCount('data.data', 1);
        $this->callFilter('member.index', ['bill_kind' => BillKind::EMAIL->value])
            ->assertInertiaCount('data.data', 2);
        $this->callFilter('member.index', [])
            ->assertInertiaCount('data.data', 3);
    }

    public function testItHandlesGroupIds(): void
    {
        $group = Group::factory()->create();
        $otherGroup = Group::factory()->create();
        $thirdGroup = Group::factory()->create();
        Member::factory()->defaults()->for($group)->create();
        Member::factory()->defaults()->count(2)->for($otherGroup)->create();
        Member::factory()->defaults()->count(3)->for($thirdGroup)->create();

        sleep(1);
        $this->callFilter('member.index', ['group_ids' => [$group->id]])
            ->assertInertiaCount('data.data', 1);
        $this->callFilter('member.index', ['group_ids' => [$otherGroup->id]])
            ->assertInertiaCount('data.data', 2);
        $this->callFilter('member.index', ['group_ids' => [$otherGroup->id, $thirdGroup->id]])
            ->assertInertiaCount('data.data', 5);
        $this->callFilter('member.index', [])
            ->assertInertiaCount('data.data', 6);
    }

    public function testItHandlesActivitiesAndSubactivities(): void
    {
        $mitglied = Activity::factory()->name('€ Mitglied')->create();
        $schnuppermitglied = Activity::factory()->name('Schnuppermitgliedschaft')->create();
        $woelfling = Subactivity::factory()->name('Wölfling')->create();
        $rover = Subactivity::factory()->name('Rover')->create();
        Member::factory()->defaults()->create();
        Member::factory()->defaults()->count(2)->has(Membership::factory()->for($mitglied)->for($woelfling))->create();
        Member::factory()->defaults()->count(3)->has(Membership::factory()->for($schnuppermitglied)->for($rover))->create();

        sleep(1);
        $this->callFilter('member.index', ['activity_ids' => [$mitglied->id]])
            ->assertInertiaCount('data.data', 2);
        $this->callFilter('member.index', ['subactivity_ids' => [$woelfling->id]])
            ->assertInertiaCount('data.data', 2);
        $this->callFilter('member.index', ['subactivity_ids' => [$rover->id]])
            ->assertInertiaCount('data.data', 3);
        $this->callFilter('member.index', ['activity_ids' => [$schnuppermitglied->id], 'subactivity_ids' => [$woelfling->id]])
            ->assertInertiaCount('data.data', 0);
        $this->callFilter('member.index', ['activity_ids' => [$schnuppermitglied->id], 'subactivity_ids' => [$rover->id]])
            ->assertInertiaCount('data.data', 3);
        $this->callFilter('member.index', [])
            ->assertInertiaCount('data.data', 6);
    }

    public function testItHandlesActivityAndSubactivityForSingleMembership(): void
    {
        $mitglied = Activity::factory()->name('€ Mitglied')->create();
        $schnuppermitglied = Activity::factory()->name('Schnuppermitgliedschaft')->create();
        $woelfling = Subactivity::factory()->name('Wölfling')->create();
        $rover = Subactivity::factory()->name('Rover')->create();
        Member::factory()->defaults()
            ->has(Membership::factory()->for($mitglied)->for($woelfling))
            ->has(Membership::factory()->for($schnuppermitglied)->for($rover))
            ->create();

        sleep(1);
        $this->callFilter('member.index', ['activity_ids' => [$mitglied->id, 5, 6], 'subactivity_ids' => [$rover->id, 8, 9]])
            ->assertInertiaCount('data.data', 0);
    }

    public function testItIgnoresInactiveMemberships(): void
    {
        $mitglied = Activity::factory()->name('€ Mitglied')->create();
        $woelfling = Subactivity::factory()->name('Wölfling')->create();
        Member::factory()->defaults()->has(Membership::factory()->for($mitglied)->for($woelfling)->ended())->create();

        sleep(1);
        $this->callFilter('member.index', ['activity_ids' => [$mitglied->id]])
            ->assertInertiaCount('data.data', 0);
        $this->callFilter('member.index', ['subactivity_ids' => [$woelfling->id]])
            ->assertInertiaCount('data.data', 0);
    }

    public function testItListensForMembershipDeletion(): void
    {
        $mitglied = Activity::factory()->name('€ Mitglied')->create();
        $member = Member::factory()->defaults()->has(Membership::factory()->for($mitglied))->create();
        $member->memberships->first()->delete();


        sleep(1);
        $this->callFilter('member.index', ['activity_ids' => [$mitglied->id]])
            ->assertInertiaCount('data.data', 0);
    }

    public function testItFiltersForMemberships(): void
    {
        $mitglied = Activity::factory()->create();
        $woelfling = Subactivity::factory()->create();
        $juffi = Subactivity::factory()->create();
        $group = Group::factory()->create();
        Member::factory()->defaults()->has(Membership::factory()->for($mitglied)->for($woelfling)->for($group))->create();
        Member::factory()->defaults()->has(Membership::factory()->for($mitglied)->for($juffi)->for($group))->create();
        Member::factory()->defaults()
            ->has(Membership::factory()->for($mitglied)->for($woelfling)->for($group))
            ->has(Membership::factory()->for($mitglied)->for($juffi)->for($group))
            ->create();

        sleep(1);
        $this->callFilter('member.index', ['memberships' => [
            ['group_ids' => [$group->id], 'activity_ids' => [$mitglied->id], 'subactivity_ids' => [$woelfling->id]]
        ]])->assertInertiaCount('data.data', 2);
        $this->callFilter('member.index', ['memberships' => [
            ['group_ids' => [$group->id], 'activity_ids' => [$mitglied->id], 'subactivity_ids' => [$juffi->id]]
        ]])->assertInertiaCount('data.data', 2);
        $this->callFilter('member.index', ['memberships' => [
            ['group_ids' => [$group->id], 'activity_ids' => [$mitglied->id], 'subactivity_ids' => [$juffi->id, $woelfling->id]],
        ]])->assertInertiaCount('data.data', 3);
        $this->callFilter('member.index', ['memberships' => [
            ['group_ids' => [$group->id], 'activity_ids' => [$mitglied->id], 'subactivity_ids' => [$woelfling->id]],
            ['group_ids' => [$group->id], 'activity_ids' => [$mitglied->id], 'subactivity_ids' => [$juffi->id]],
        ]])->assertInertiaCount('data.data', 1);
    }

    public function testGroupOfMembershipsFilterCanBeEmpty(): void
    {
        $mitglied = Activity::factory()->create();
        $woelfling = Subactivity::factory()->create();
        $group = Group::factory()->create();
        Member::factory()->defaults()->has(Membership::factory()->for($mitglied)->for($woelfling)->for($group))->create();

        sleep(1);
        $this->callFilter('member.index', ['memberships' => [
            ['group_ids' => [], 'activity_ids' => [$mitglied->id], 'subactivity_ids' => [$woelfling->id]],
        ]])->assertInertiaCount('data.data', 1);
        $this->callFilter('member.index', ['memberships' => [
            ['group_ids' => [$group->id], 'activity_ids' => [], 'subactivity_ids' => [$woelfling->id]],
        ]])->assertInertiaCount('data.data', 1);
        $this->callFilter('member.index', ['memberships' => [
            ['group_ids' => [$group->id], 'activity_ids' => [$mitglied->id], 'subactivity_ids' => []],
        ]])->assertInertiaCount('data.data', 1);
    }

    public function testItFiltersForSearchButNotForPayments(): void
    {
        Member::factory()->defaults()
            ->has(InvoicePosition::factory()->for(Invoice::factory()))
            ->create(['firstname' => 'firstname']);
        Member::factory()->defaults()->create(['firstname' => 'firstname']);

        sleep(1);
        $this->callFilter('member.index', ['search' => 'firstname', 'ausstand' => true])
            ->assertInertiaCount('data.data', 1);
    }

    public function testItIgnoresPaidInvoices(): void
    {
        Member::factory()->defaults()
            ->has(InvoicePosition::factory()->for(Invoice::factory()->status(InvoiceStatus::PAID)))
            ->create();

        sleep(1);
        $this->callFilter('member.index', ['ausstand' => true])
            ->assertInertiaCount('data.data', 0);
    }


    public function testItIncludesMembers(): void
    {
        $member = Member::factory()->defaults()->create(['birthday' => null, 'location' => '']);

        sleep(1);
        $this->callFilter('member.index', ['hasBirthday' => true, 'hasFullAddress' => false])
            ->assertInertiaCount('data.data', 0);
        $this->callFilter('member.index', ['hasBirthday' => true, 'hasFullAddress' => false, 'include' => [$member->id]])
            ->assertInertiaCount('data.data', 1);
    }

    public function testItExcludesMembers(): void
    {
        $member = Member::factory()->defaults()->create(['birthday' => null]);

        sleep(1);
        $this->callFilter('member.index', ['hasBirthday' => false, 'exclude' => [$member->id]])
            ->assertInertiaCount('data.data', 0);
    }
}
