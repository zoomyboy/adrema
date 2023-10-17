<?php

namespace Tests\Feature\Course;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\CourseFake;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

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
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->for(Course::factory()), 'courses')->createOne();
        $newCourse = Course::factory()->inNami(789)->create();

        $response = $this->patch("/course/{$member->courses->first()->id}", array_merge([
            'course_id' => $newCourse->id,
            'completed_at' => '1999-02-03',
            'event_name' => '::newevent::',
            'organizer' => '::org::',
        ], $payload));

        $this->assertErrors($errors, $response);
    }

    public function testItUpdatesACourse(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->loginNami();
        app(CourseFake::class)->updatesSuccessfully(123, 999);
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->inNami(999)->for(Course::factory()), 'courses')->createOne();
        $newCourse = Course::factory()->inNami(789)->create();

        $this->patch("/course/{$member->courses->first()->id}", array_merge([
            'course_id' => $newCourse->id,
            'completed_at' => '1999-02-03',
            'event_name' => '::newevent::',
            'organizer' => '::neworg::',
        ]));

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

    public function testItThrowsErrorWhenLoginIsWrong(): void
    {
        $this->login()->withNamiSettings();
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->inNami(999)->for(Course::factory()), 'courses')->createOne();
        $newCourse = Course::factory()->inNami(789)->create();

        $response = $this->patch("/course/{$member->courses->first()->id}", array_merge([
            'course_id' => $newCourse->id,
            'completed_at' => '1999-02-03',
            'event_name' => '::newevent::',
            'organizer' => '::neworg::',
        ]));

        $this->assertErrors(['nami' => 'NaMi Login fehlgeschlagen.'], $response);
    }

    public function testItReceivesUnknownErrors(): void
    {
        $this->login()->loginNami();
        app(CourseFake::class)->failsUpdating(123, 999);
        $member = Member::factory()->defaults()->inNami(123)->has(CourseMember::factory()->inNami(999)->for(Course::factory()), 'courses')->createOne();
        $newCourse = Course::factory()->inNami(789)->create();

        $response = $this->patch("/course/{$member->courses->first()->id}", [
            'course_id' => $newCourse->id,
            'completed_at' => '2021-01-02',
            'event_name' => '::event::',
            'organizer' => '::org::',
        ]);

        $response->assertSessionHasErrors(['id' => 'Unbekannter Fehler']);
    }
}
