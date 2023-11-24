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
        $member = Member::factory()->defaults()->create(['salutation' => 'Doktor', 'firstname' => 'Max', 'comment' => 'Lorem bla']);
        $activity = Activity::factory()->inNami(66)->hasAttached(Subactivity::factory()->inNami(56)->name('Biber'))->name('€ Mitglied')->create();
        $subactivity = $activity->subactivities->first();

        $response = $this->get(route('member.edit', ['member' => $member]));

        $this->assertInertiaHas('Biber', $response, "meta.formSubactivities.{$activity->id}.{$subactivity->id}");
        $this->assertInertiaHas('€ Mitglied', $response, "meta.formActivities.{$activity->id}");
        $this->assertInertiaHas('Max', $response, 'data.firstname');
        $this->assertInertiaHas('Doktor', $response, 'data.salutation');
        $this->assertInertiaHas('Lorem bla', $response, 'data.comment');
        $this->assertInertiaHas('edit', $response, 'mode');
        $this->assertInertiaHas(false, $response, 'conflict');
        $this->assertInertiaHas(['name' => 'E-Mail', 'id' => 'E-Mail'], $response, 'meta.billKinds.0');
    }

    public function testItDisplaysEducation(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->create([
                'efz' => '2022-09-20',
                'ps_at' => '2022-04-20',
                'more_ps_at' => '2022-06-02',
                'without_education_at' => '2022-06-03',
                'without_efz_at' => '2022-06-04',
                'recertified_at' => '2022-06-04',
                'has_vk' => true,
                'has_svk' => true,
                'multiply_pv' => true,
                'multiply_more_pv' => true,
            ]);

        $response = $this->get(route('member.edit', ['member' => $member]));

        $this->assertInertiaHas([
            'efz' => '2022-09-20',
            'ps_at' => '2022-04-20',
            'more_ps_at' => '2022-06-02',
            'without_education_at' => '2022-06-03',
            'without_efz_at' => '2022-06-04',
            'recertified_at' => '2022-06-04',
            'has_vk' => true,
            'has_svk' => true,
            'multiply_pv' => true,
            'multiply_more_pv' => true,
        ], $response, 'data');
    }

    public function testItDisplaysSystem(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()
            ->defaults()
            ->emailBillKind()
            ->create();

        $response = $this->get(route('member.edit', ['member' => $member]));

        $this->assertInertiaHas([
            'bill_kind' => 'E-Mail',
        ], $response, 'data');
    }
}
