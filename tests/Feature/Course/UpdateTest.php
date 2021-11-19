<?php

namespace Tests\Feature\Course;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Backend\FakeBackend;
use Zoomyboy\LaravelNami\Fakes\CourseFake;

class UpdateTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @return array<string, array{payload: array<string, mixed>, errors: array<string, mixed>}>
     */
    public function validationDataProvider(): array
    {
        return [
            'course_id_missing' => [
                'payload' => ['course_id' => null],
                'errors' => ['course_id' => 'Baustein ist erforderlich.'],
            ],
            'course_id_invalid' => [
                'payload' => ['course_id' => 999],
                'errors' => ['course_id' => 'Baustein ist nicht vorhanden.'],
            ],
            'completed_at_required' => [
                'payload' => ['completed_at' => ''],
                'errors' => ['completed_at' => 'Datum ist erforderlich.'],
            ],
            'completed_at_not_date' => [
                'payload' => ['completed_at' => '123'],
                'errors' => ['completed_at' => 'Datum muss ein gültiges Datum sein.'],
            ],
            'event_name_required' => [
                'payload' => ['event_name' => ''],
                'errors' => ['event_name' => 'Veranstaltung ist erforderlich.'],
            ],
            'organizer' => [
                'payload' => ['organizer' => ''],
                'errors' => ['organizer' => 'Veranstalter ist erforderlich.'],
            ],
        ];
    }

    /**
     * @param array<string, string> $payload
     * @param array<string, string> $errors
     * @dataProvider validationDataProvider
     */
    public function testItValidatesInput(array $payload, array $errors): void
    {
        $this->login()->init();
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->for(Course::factory()), 'courses')->createOne();
        $newCourse = Course::factory()->inNami(789)->create();

        $response = $this->patch("/member/{$member->id}/course/{$member->courses->first()->id}", array_merge([
            'course_id' => $newCourse->id,
            'completed_at' => '1999-02-03',
            'event_name' => '::newevent::',
            'organizer' => '::org::',
        ], $payload));

        $response->assertSessionHasErrors($errors);
    }

    public function testItUpdatesACourse(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->init();
        app(CourseFake::class)->updatesSuccessful(123, 999);
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->inNami(999)->for(Course::factory()), 'courses')->createOne();
        $newCourse = Course::factory()->inNami(789)->create();

        $response = $this->patch("/member/{$member->id}/course/{$member->courses->first()->id}", array_merge([
            'course_id' => $newCourse->id,
            'completed_at' => '1999-02-03',
            'event_name' => '::newevent::',
            'organizer' => '::neworg::',
        ]));

        $response->assertRedirect("/member");
        $this->assertDatabaseHas('course_members', [
            'member_id' => $member->id,
            'course_id' => $newCourse->id,
            'event_name' => '::newevent::',
            'organizer' => '::neworg::',
            'completed_at' => '1999-02-03',
            'nami_id' => 999,
        ]);
        app(CourseFake::class)->assertUpdated(123, 999, [
            'bausteinId' => 789,
            'veranstalter' => '::neworg::',
            'vstgName' => '::newevent::',
            'vstgTag' => '1999-02-03T00:00:00',
        ]);
    }

    /*
    public function testItReceivesUnknownErrors(): void
    {
        $this->login()->init();
        $member = Member::factory()->defaults()->inNami(123)->createOne();
        $course = Course::factory()->inNami(456)->createOne();
        app(CourseFake::class)->doesntCreateWithError(123);

        $response = $this->post("/member/{$member->id}/course", [
            'course_id' => $course->id,
            'completed_at' => '2021-01-02',
            'event_name' => '::event::',
            'organizer' => '::org::',
        ]);
                         
        $response->assertSessionHasErrors(['id' => 'Unbekannter Fehler']);
        $this->assertDatabaseCount('course_member', 0);
    }
     */

}
