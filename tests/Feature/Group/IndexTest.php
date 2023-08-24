<?php

namespace Tests\Feature\Group;

use App\Activity;
use App\Group;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysAllActivitiesAndSubactivities(): void
    {
        $this->login()->loginNami();

        $leiter = Activity::factory()->name('Leiter*in')->hasAttached(Subactivity::factory()->name('Rover'))->create();
        $intern = Activity::factory()->name('Intern')->hasAttached(Subactivity::factory()->name('Lager'))->create();
        $group = Group::factory()->create();

        $response = $this->get('/group');

        $this->assertInertiaHas('Leiter*in', $response, "activities.{$leiter->id}");
        $this->assertInertiaHas('Intern', $response, "activities.{$intern->id}");
        $this->assertInertiaHas('Rover', $response, "subactivities.{$leiter->id}.{$leiter->subactivities->first()->id}");
        $this->assertInertiaHas('Lager', $response, "subactivities.{$intern->id}.{$intern->subactivities->first()->id}");
        $this->assertInertiaHas($group->name, $response, "groups.{$group->id}");
    }
}
