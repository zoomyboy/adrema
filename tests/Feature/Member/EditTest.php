<?php

namespace Tests\Feature\Member;

use App\Activity;
use App\Member\Member;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EditTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysEditPage(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->create(['firstname' => 'Max']);
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('Biber'))->name('€ Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        $response = $this->get(route('member.edit', ['member' => $member]));

        $this->assertInertiaHas('Biber', $response, "subactivities.{$activity->id}.{$subactivity->id}");
        $this->assertInertiaHas('€ Mitglied', $response, "activities.{$activity->id}");
        $this->assertInertiaHas('Max', $response, 'data.firstname');
        $this->assertInertiaHas('edit', $response, 'mode');
        $this->assertInertiaHas(false, $response, 'conflict');
    }
}
