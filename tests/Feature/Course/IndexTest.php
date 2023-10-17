<?php

namespace Tests\Feature\Course;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItShowsCourses(): void
    {
        $this->login()->withNamiSettings();
        $member = Member::factory()->defaults()->has(CourseMember::factory()->for(Course::factory()->state(['name' => '2a']))->state(['event_name' => 'WE', 'organizer' => 'DPSG', 'completed_at' => now()->subDays(2)]), 'courses')->create();

        $this->get("/member/{$member->id}/course")
            ->assertJsonPath('data.0.course_name', '2a')
            ->assertJsonPath('data.0.event_name', 'WE')
            ->assertJsonPath('data.0.organizer', 'DPSG')
            ->assertJsonPath('data.0.links.update', route('course.update', ['course' => $member->courses->first()->id]))
            ->assertJsonPath('data.0.links.destroy', route('course.destroy', ['course' => $member->courses->first()->id]))
            ->assertJsonPath('data.0.completed_at_human', now()->subDays(2)->format('d.m.Y'))
            ->assertJsonPath('meta.links.store', route('member.course.store', ['member' => $member]))
            ->assertJsonPath('meta.default.completed_at', null)
            ->assertJsonPath('meta.default.course_id', null)
            ->assertJsonPath('meta.default.event_name', '')
            ->assertJsonPath('meta.default.organizer', '')
            ->assertJsonPath('meta.courses.0.name', '2a')
            ->assertJsonPath('meta.courses.0.id', Course::first()->id);
    }
}
