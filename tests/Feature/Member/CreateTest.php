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

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        Country::factory()->create(['name' => 'Deutschland']);
    }

    public function testItDisplaysCreatePage(): void
    {
        $activity = Activity::factory()->inNami(5)->hasAttached(Subactivity::factory()->inNami(23)->name('Biber'))->name('€ Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        $response = $this->get(route('member.create'));

        $this->assertInertiaHas('Biber', $response, "meta.formSubactivities.{$activity->id}.{$subactivity->id}");
        $this->assertInertiaHas('€ Mitglied', $response, "meta.formActivities.{$activity->id}");
        $this->assertInertiaHas(['name' => 'E-Mail', 'id' => 'E-Mail'], $response, 'meta.billKinds.0');

        $this->assertInertiaHas([
            'efz' => null,
            'ps_at' => null,
            'more_ps_at' => null,
            'without_education_at' => null,
            'without_efz_at' => null,
            'address' => '',
        ], $response, 'data');
    }

    public function testItDoesntDisplayActivitiesAndSubactivitiesNotInNami(): void
    {
        Activity::factory()->hasAttached(Subactivity::factory()->name('Biber'))->name('€ Mitglied')->create();

        $response = $this->get(route('member.create'));

        $this->assertCount(0, $this->inertia($response, 'meta.formCreateSubactivities'));
        $this->assertCount(0, $this->inertia($response, 'meta.formCreateActivities'));
    }
}
