<?php

namespace Tests\Feature\Course;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Backend\FakeBackend;
use Zoomyboy\LaravelNami\Fakes\CourseFake;

class DeleteTest extends TestCase
{

    use DatabaseTransactions;

    public function testItDeletesACourse(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->init();
        app(CourseFake::class)->deleteSuccessful(123, 999);
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->inNami(999)->for(Course::factory()), 'courses')->createOne();

        $this->delete("/member/{$member->id}/course/{$member->courses->first()->id}");

        $this->assertDatabaseCount('course_members', 0);
        app(CourseFake::class)->assertDeleted(123, 999);
    }

    public function testItReceivesUnknownErrors(): void
    {
        $this->login()->init();
        app(CourseFake::class)->deleteFailed(123, 999);
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->inNami(999)->for(Course::factory()), 'courses')->createOne();

        $response = $this->delete("/member/{$member->id}/course/{$member->courses->first()->id}");

        $this->assertDatabaseCount('course_members', 1);
                         
        $response->assertSessionHasErrors(['id' => 'Unbekannter Fehler']);
    }

}
