<?php

namespace Tests\Feature\Membership;

use App\Group;
use App\Member\Member;
use App\Member\Membership;
use Generator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use DatabaseTransactions;

    public function testItShowsActivityAndSubactivityNamesOfMember(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $group = Group::factory()->create(['name' => 'aaaaaaaa']);
        $member = Member::factory()
            ->defaults()
            ->for($group)
            ->has(Membership::factory()->for($group)->in('€ Mitglied', 122, 'Wölfling', 234)->from('2022-11-02'))
            ->create();
        $membership = $member->memberships->first();

        $this->postJson("/api/member/{$member->id}/membership")
            ->assertJsonPath('data.0.activity_id', $membership->activity_id)
            ->assertJsonPath('data.0.subactivity_id', $membership->subactivity_id)
            ->assertJsonPath('data.0.activity_name', '€ Mitglied')
            ->assertJsonPath('data.0.subactivity_name', 'Wölfling')
            ->assertJsonPath('data.0.human_date', '02.11.2022')
            ->assertJsonPath('data.0.group_id', $group->id)
            ->assertJsonPath('data.0.id', $membership->id)
            ->assertJsonPath('data.0.links.update', route('membership.update', ['membership' => $membership]))
            ->assertJsonPath('data.0.links.destroy', route('membership.destroy', ['membership' => $membership]))
            ->assertJsonPath('meta.default.activity_id', null)
            ->assertJsonPath('meta.default.group_id', $group->id)
            ->assertJsonPath('meta.groups.0.id', $group->id)
            ->assertJsonPath('meta.activities.0.id', $membership->activity_id)
            ->assertJsonPath("meta.subactivities.{$membership->activity_id}.0.id", $membership->subactivity_id)
            ->assertJsonPath('meta.links.store', route('member.membership.store', ['member' => $member]));
    }

    public function membershipDataProvider(): Generator
    {
        yield [now()->subMonths(2), null, true];
        yield [now()->subMonths(2), now()->subDay(), false];
        yield [now()->addDays(2), null, false];
    }

    /**
     * @dataProvider membershipDataProvider
     */
    public function testItShowsIfMembershipIsActive(Carbon $from, ?Carbon $to, bool $isActive): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->in('€ LeiterIn', 455, 'Pfadfinder', 15)->state(['from' => $from, 'to' => $to]))
            ->create();

        $this->postJson("/api/member/{$member->id}/membership")
            ->assertJsonPath('data.0.is_active', $isActive);
    }
}
