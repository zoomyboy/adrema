<?php

namespace Tests\Feature\Membership;

use App\Group;
use App\Member\Member;
use App\Member\Membership;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;
use Zoomyboy\LaravelNami\Fakes\MembershipFake;

class DestroyTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2022-02-03 03:00:00'));
        $this->login()->loginNami();
    }

    public function testItDestroysAMembership(): void
    {
        $this->withoutExceptionHandling();
        app(MembershipFake::class)
            ->destroysSuccessfully(6, 1300)
            ->shows(6, [
                'id' => 1300,
                'gruppierungId' => 1400,
                'taetigkeitId' => 1,
                'untergliederungId' => 6,
                'aktivVon' => '2017-02-11 00:00:00',
                'aktivBis' => null,
            ]);
        app(MemberFake::class)->shows(1400, 6, ['version' => 1506]);
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->has(Membership::factory()->inNami(1300)->in('€ Mitglied', 1, 'Rover', 6))
            ->inNami(6)
            ->create();

        $response = $this->from('/member')->delete("/member/{$member->id}/membership/{$member->memberships->first()->id}");

        $response->assertRedirect('/member');
        $this->assertEquals(1506, $member->fresh()->version);
        $this->assertDatabaseMissing('memberships', [
            'member_id' => $member->id,
            'nami_id' => 1300,
        ]);
        app(MembershipFake::class)->assertDeleted(6, 1300);
    }
}
