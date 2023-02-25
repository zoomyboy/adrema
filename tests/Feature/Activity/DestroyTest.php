<?php

namespace Tests\Feature\Activity;

use App\Activity;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDeletesAnActivity(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->hasAttached(Subactivity::factory())->create();

        $response = $this->delete(route('activity.destroy', ['activity' => $activity]));

        $response->assertRedirect('/activity');
        $this->assertDatabaseCount('activities', 0);
    }

    public function testItCannotDeleteAnActivityThatHasMemberships(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->create();
        Member::factory()->defaults()->has(Membership::factory()->for($activity))->create();

        $response = $this->delete(route('activity.destroy', ['activity' => $activity]));

        $response->assertSessionHasErrors(['activity' => 'Tätigkeit besitzt noch Mitglieder.']);
    }

    public function testItCannotDeleteActivityInNami(): void
    {
        $this->login()->loginNami();
        $activity = Activity::factory()->inNami(66)->create();

        $response = $this->delete(route('activity.destroy', ['activity' => $activity]));

        $response->assertSessionHasErrors(['activity' => 'Tätigkeit ist in NaMi.']);
    }


}
