<?php

namespace Tests\Feature\Member;

use App\Member\DeleteJob;
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

        Queue::assertPushed(DeleteJob::class, fn ($job) => $job->memberId === $member->id);
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

        Queue::assertNotPushed(DeleteJob::class);
        $this->assertDatabaseMissing('members', [
            'id' => $member->id,
        ]);
    }

    public function testTheActionDeletesNamiMember(): void
    {
        app(MemberFake::class)->deletes(123, Carbon::parse('yesterday'));
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->inNami(123)->create();

        dispatch(new DeleteJob($member));

        app(MemberFake::class)->assertDeleted(123, Carbon::parse('yesterday'));
        $this->assertNull($member->fresh()->nami_id);
    }
}
