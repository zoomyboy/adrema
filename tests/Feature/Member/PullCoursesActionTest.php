<?php

namespace Tests\Feature\Member;

use App\Actions\PullCoursesAction;
use App\Activity;
use App\Country;
use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Fee;
use App\Gender;
use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Data\Course as NamiCourse;
use Zoomyboy\LaravelNami\Fakes\CourseFake;

class PullCoursesActionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Subscription::factory()->name('test')->for(Fee::factory()->inNami(300))->create();
        Gender::factory()->inNami(303)->create();
        Country::factory()->inNami(302)->create();
        Nationality::factory()->inNami(1054)->create();
        $this->loginNami();
    }

    public function testItDoesntSyncCoursesOfNonNamiMembers(): void
    {
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(1000)->name('SG Wald'))->create();

        app(PullCoursesAction::class)->handle($member);

        Http::assertSentCount(0);
    }

    public function testFetchCourses(): void
    {
        $activity = Activity::factory()->inNami(1003)->name('Tätigkeit')->create();
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(1000)->name('SG Wald'))->inNami(1001)->create();
        $course = Course::factory()->name('BS')->inNami(11)->create();
        app(CourseFake::class)->fetches(1001, [50])->shows(1001, NamiCourse::factory()->toCourse([
            'courseId' => 11,
            'organizer' => 'TTT',
            'eventName' => 'Schulung',
            'completedAt' => '2021-06-29 00:00:00',
            'id' => 50,
        ]));

        app(PullCoursesAction::class)->handle($member);

        $this->assertDatabaseHas('course_members', [
            'nami_id' => 50,
            'member_id' => $member->id,
            'organizer' => 'TTT',
            'completed_at' => '2021-06-29',
            'event_name' => 'Schulung',
            'course_id' => $course->id,
        ]);
    }

    public function testDeleteExistingCourses(): void
    {
        $activity = Activity::factory()->inNami(1003)->name('Tätigkeit')->create();
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1000)->name('SG Wald'))
            ->has(CourseMember::factory()->for(Course::factory()->inNami(50))->inNami(55), 'courses')
            ->inNami(1001)
            ->create();
        app(CourseFake::class)->fetches(1001, []);

        app(PullCoursesAction::class)->handle($member);

        $this->assertDatabaseCount('course_members', 0);
    }

    public function testCourseIsUpdated(): void
    {
        $activity = Activity::factory()->inNami(1003)->name('Tätigkeit')->create();
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1000)->name('SG Wald'))
            ->has(CourseMember::factory()->for(Course::factory()->inNami(50))->inNami(55), 'courses')
            ->inNami(1001)
            ->create();
        app(CourseFake::class)->fetches(1001, [55])->shows(1001, NamiCourse::factory()->toCourse(['id' => 55, 'courseId' => 50, 'organizer' => 'ZZU']));

        app(PullCoursesAction::class)->handle($member);

        $this->assertDatabaseCount('course_members', 1);
        $this->assertDatabaseHas('course_members', [
            'organizer' => 'ZZU',
        ]);
    }
}
