<?php

namespace Tests\Feature\Member;

use App\Member\Member;
use App\Member\Membership;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use DatabaseTransactions;

    public function testItShowsSingleMember(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->has(Membership::factory()->in('€ LeiterIn', 5, 'Jungpfadfinder', 88)->state(['created_at' => '2022-11-19 05:00:00']))
            ->defaults()->create(['firstname' => 'Max']);

        $response = $this->get("/member/{$member->id}");

        $this->assertInertiaHas('Max', $response, 'data.firstname');
        $this->assertInertiaHas([
            'activity_name' => '€ LeiterIn',
            'id' => $member->memberships->first()->id,
            'human_date' => '19.11.2022',
         ], $response, 'data.memberships.0');
    }
}
