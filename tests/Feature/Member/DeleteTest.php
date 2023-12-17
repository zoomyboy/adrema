<?php

namespace Tests\Feature\Member;

use Illuminate\Support\Str;
use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Invoice\Models\Invoice;
use App\Invoice\Models\InvoicePosition;
use App\Lib\Events\JobFailed;
use App\Lib\Events\JobFinished;
use App\Lib\Events\JobStarted;
use App\Member\Actions\MemberDeleteAction;
use App\Member\Actions\NamiDeleteMemberAction;
use App\Member\Member;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Throwable;

class DeleteTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake([JobStarted::class, JobFinished::class, JobFailed::class]);
    }

    public function testItFiresJob(): void
    {
        Queue::fake();
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->inNami(123)->create();

        $response = $this->from('/member')->delete("/member/{$member->id}");

        $response->assertRedirect('/member');

        Event::assertDispatched(JobStarted::class);
        MemberDeleteAction::assertPushed(fn ($action, $parameters) => $parameters[0] === $member->id);
    }

    public function testItDeletesMemberFromNami(): void
    {
        $this->login()->loginNami();
        NamiDeleteMemberAction::partialMock()->shouldReceive('handle')->with(123)->once();
        $member = Member::factory()->defaults()->inNami(123)->create();

        MemberDeleteAction::run($member->id);

        $this->assertDatabaseMissing('members', [
            'id' => $member->id,
        ]);
    }

    public function testItDoesntRunActionWhenMemberIsNotInNami(): void
    {
        $this->login()->loginNami();
        NamiDeleteMemberAction::partialMock()->shouldReceive('handle')->never();
        $member = Member::factory()->defaults()->create();

        MemberDeleteAction::run($member->id);
    }


    public function testItDeletesMembersWithCourses(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->has(CourseMember::factory()->for(Course::factory()), 'courses')->create();

        MemberDeleteAction::run($member->id);

        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }

    public function testItDeletesInvoicePositions(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->create();
        $member2 = Member::factory()->defaults()->create();
        Invoice::factory()
            ->has(InvoicePosition::factory()->for($member), 'positions')
            ->has(InvoicePosition::factory()->for($member2), 'positions')
            ->create();

        MemberDeleteAction::run($member->id);

        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseCount('invoice_positions', 1);
    }

    public function testItDeletesInvoicePositionsAndInvoices(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->create();
        Invoice::factory()
            ->has(InvoicePosition::factory()->for($member), 'positions')
            ->has(InvoicePosition::factory()->for($member), 'positions')
            ->create();

        MemberDeleteAction::run($member->id);

        $this->assertDatabaseCount('invoices', 0);
        $this->assertDatabaseCount('invoice_positions', 0);
    }

    public function testItFiresEventWhenFinished(): void
    {
        Event::fake([JobStarted::class, JobFinished::class]);
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);
        $uuid = Str::uuid();

        MemberDeleteAction::dispatch($member->id, $uuid);

        Event::assertNotDispatched(JobStarted::class);
        Event::assertDispatched(JobFinished::class, fn ($event) => $event->message === 'Mitglied Max Muster gelöscht' && $event->jobId->serialize() === $uuid->serialize());
    }

    public function testItFiresEventWhenDeletingFailed(): void
    {
        Event::fake([JobStarted::class, JobFinished::class, JobFailed::class]);
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);
        MemberDeleteAction::partialMock()->shouldReceive('handle')->andThrow(new Exception('sorry'));
        $uuid = Str::uuid();

        try {
            MemberDeleteAction::dispatch($member->id, $uuid);
        } catch (Throwable) {
        }

        Event::assertNotDispatched(JobStarted::class);
        Event::assertDispatched(JobFailed::class, fn ($event) => $event->message === 'Löschen von Max Muster fehlgeschlagen.' && $event->jobId->serialize() === $uuid->serialize());
        Event::assertNotDispatched(JobFinished::class);
    }
}
