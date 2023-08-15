<?php

namespace Tests\Feature\Member;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Member\Actions\NamiDeleteMemberAction;
use App\Member\Member;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

class DeleteTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDeletesMemberFromNami(): void
    {
        Queue::fake();
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->inNami(123)->create();

        $response = $this->from('/member')->delete("/member/{$member->id}");

        $response->assertRedirect('/member');

        NamiDeleteMemberAction::assertPushed();
        $this->assertDatabaseMissing('members', [
            'id' => $member->id,
        ]);
    }

    public function testItDoesntRunActionWhenMemberIsNotInNami(): void
    {
        Queue::fake();
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->create();

        $response = $this->from('/member')->delete("/member/{$member->id}");

        $response->assertRedirect('/member');

        Queue::assertNotPushed(NamiDeleteMemberAction::class);
        $this->assertDatabaseMissing('members', [
            'id' => $member->id,
        ]);
    }

    public function testTheActionDeletesNamiMember(): void
    {
        app(MemberFake::class)->deletes(123, Carbon::parse('yesterday'));
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->inNami(123)->create();

        NamiDeleteMemberAction::dispatch(123);

        app(MemberFake::class)->assertDeleted(123, Carbon::parse('yesterday'));
    }

    public function testItDeletesMembersWithCourses(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->has(CourseMember::factory()->for(Course::factory()), 'courses')->create();

        $member->delete();

        $this->assertDatabaseCount('members', 0);
    }
}
