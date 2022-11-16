<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Country;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysCreatePage(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        Country::factory()->create(['name' => 'Deutschland']);
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('Biber'))->name('€ Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        $response = $this->get(route('member.create'));

        $this->assertInertiaHas('Biber', $response, "subactivities.{$activity->id}.{$subactivity->id}");
        $this->assertInertiaHas('€ Mitglied', $response, "activities.{$activity->id}");
    }
}
