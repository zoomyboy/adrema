<?php

namespace Tests\Feature\Course;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\CourseFake;

class DeleteTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDeletesACourse(): void
    {
        $this->login()->loginNami();
        app(CourseFake::class)->deletesSuccessfully(123, 999);
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->inNami(999)->for(Course::factory()), 'courses')->createOne();

        $this->delete("//course/{$member->courses->first()->id}");

        $this->assertDatabaseCount('course_members', 0);
        app(CourseFake::class)->assertDeleted(123, 999);
    }

    public function testItReceivesUnknownErrors(): void
    {
        $this->login()->loginNami();
        app(CourseFake::class)->failsDeleting(123, 999);
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->inNami(999)->for(Course::factory()), 'courses')->createOne();

        $response = $this->delete("/course/{$member->courses->first()->id}");

        $this->assertDatabaseCount('course_members', 1);
        $response->assertSessionHasErrors(['id' => 'Unbekannter Fehler']);
    }
}
