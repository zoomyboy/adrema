<?php

namespace Tests\Feature\Payment;

use App\Lib\Events\JobFinished;
use App\Lib\Events\JobStarted;
use App\Lib\Events\ReloadTriggered;
use App\Member\Member;
use App\Payment\Status;
use App\Payment\Subscription;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StoreTest extends TestCase
{

    use DatabaseTransactions;

    public function testItStoresAPayment(): void
    {
        Event::fake([JobStarted::class, JobFinished::class, ReloadTriggered::class]);
        $this->withoutExceptionHandling()->login()->loginNami();
        $subscription = Subscription::factory()->create();
        $member = Member::factory()->defaults()->create();
        $status = Status::factory()->create();

        $this->post("/member/{$member->id}/payment", [
            'status_id' => $status->id,
            'subscription_id' => $subscription->id,
            'nr' => '2019',
        ])->assertOk();

        $this->assertDatabaseHas('payments', [
            'member_id' => $member->id,
            'status_id' => $status->id,
            'subscription_id' => $subscription->id,
            'nr' => '2019',
        ]);

        Event::assertDispatched(JobStarted::class, fn ($event) => $event->broadcastOn()[0]->name === 'jobs' && $event->message !== null);
        Event::assertDispatched(JobFinished::class, fn ($event) => $event->broadcastOn()[0]->name === 'jobs' && $event->message !== null);
        Event::assertDispatched(ReloadTriggered::class, fn ($event) => ['member', 'payment'] === $event->channels->toArray());
    }
}
