<?php

namespace Tests\Feature\Course;

use App\Course\Models\Course;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Backend\FakeBackend;
use Zoomyboy\LaravelNami\Fakes\CourseFake;

class StoreTest extends TestCase
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
        $this->login();
        $member = Member::factory()->defaults()->createOne();
        $course = Course::factory()->createOne();

        $response = $this->post("/member/{$member->id}/course", array_merge([
            'course_id' => $course->id,
            'completed_at' => '2021-01-02',
            'event_name' => '::event::',
            'organizer' => '::org::',
        ], $payload));

        $response->assertSessionHasErrors($errors);
    }

    public function testItCreatesACourse(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->inNami(123)->createOne();
        $course = Course::factory()->inNami(456)->createOne();
        app(CourseFake::class)->createsSuccessful(123, 999);

        $this->post("/member/{$member->id}/course", [
            'course_id' => $course->id,
            'completed_at' => '2021-01-02',
            'event_name' => '::event::',
            'organizer' => '::org::',
        ]);

        $this->assertDatabaseHas('course_members', [
            'member_id' => $member->id,
            'course_id' => $course->id,
            'completed_at' => '2021-01-02',
            'event_name' => '::event::',
            'organizer' => '::org::',
            'nami_id' => 999,
        ]);
        app(CourseFake::class)->assertCreated(123, [
            'bausteinId' => 456,
            'veranstalter' => '::org::',
            'vstgName' => '::event::',
            'vstgTag' => '2021-01-02T00:00:00',
        ]);
    }

    public function testItThrowsErrorWhenLoginIsWrong(): void
    {
        $this->login()->failedNami();
        $member = Member::factory()->defaults()->inNami(123)->createOne();
        $course = Course::factory()->inNami(456)->createOne();

        $response = $this->post("/member/{$member->id}/course", [
            'course_id' => $course->id,
            'completed_at' => '2021-01-02',
            'event_name' => '::event::',
            'organizer' => '::org::',
        ]);

        $this->assertErrors(['nami' => 'NaMi Login fehlgeschlagen.'], $response);

        $this->assertDatabaseMissing('course_members', [
            'member_id' => $member->id,
        ]);
    }

    public function testItReceivesUnknownErrors(): void
    {
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->inNami(123)->createOne();
        $course = Course::factory()->inNami(456)->createOne();
        app(CourseFake::class)->createFailed(123);

        $response = $this->post("/member/{$member->id}/course", [
            'course_id' => $course->id,
            'completed_at' => '2021-01-02',
            'event_name' => '::event::',
            'organizer' => '::org::',
        ]);
                         
        $response->assertSessionHasErrors(['id' => 'Unbekannter Fehler']);
        $this->assertDatabaseCount('course_members', 0);
    }

}
