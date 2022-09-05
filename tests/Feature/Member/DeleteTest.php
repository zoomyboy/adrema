<?php

namespace Tests\Feature\Member;

use App\Member\DeleteJob;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function testItDeletesMemberFromNami(): void
    {
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
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->create();

        $response = $this->from('/member')->delete("/member/{$member->id}");

        $response->assertRedirect('/member');

        Queue::assertNotPushed(DeleteJob::class);
        $this->assertDatabaseMissing('members', [
            'id' => $member->id,
        ]);
    }
}
