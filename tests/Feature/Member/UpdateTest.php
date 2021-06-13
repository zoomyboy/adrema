<?php

namespace Tests\Feature\Member;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Member\Member;
use App\Country;
use App\Nationality;
use App\Fee;
use App\Group;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_update_a_member()
    {
        $this->fakeNamiMembers([
            [ 'gruppierungId' => 12399, 'vorname' => 'Max', 'id' => 999 ]
        ]);

        $member = Member::factory()
            ->for(Country::factory())
            ->for(Group::factory()->state(['nami_id' => 12399]))
            ->for(Nationality::factory())
            ->for(Fee::factory())
            ->create(['firstname' => 'Max', 'nami_id' => 999]);

        $member->update(['firstname' => 'Jane']);

        $this->assertMemberExists(12399, [
            'vorname' => 'Jane',
            'id' => 999
        ]);
    }
}
