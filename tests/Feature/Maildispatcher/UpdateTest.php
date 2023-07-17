<?php

namespace Tests\Feature\Maildispatcher;

use App\Maildispatcher\Actions\ResyncAction;
use App\Maildispatcher\Models\Maildispatcher;
use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Types\LocalType;
use App\Member\FilterScope;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login()->loginNami();
    }

    public function testItCanUpdateFilters(): void
    {
        $dispatcher = Maildispatcher::factory()
            ->for(Mailgateway::factory()->type(LocalType::class, [])->domain('example.com'), 'gateway')
            ->filter(FilterScope::from([]))
            ->create();
        Member::factory()->defaults()->create(['email' => 'to@example.com']);

        $response = $this->patchJson("/maildispatcher/{$dispatcher->id}", [
            'name' => 'test',
            'gateway_id' => $dispatcher->gateway->id,
            'filter' => [],
        ]);

        $this->assertDatabaseHas('localmaildispatchers', [
            'from' => 'test@example.com',
            'to' => 'to@example.com',
        ]);
    }

    public function testItUpdatesCurrentMails(): void
    {
        $dispatcher = Maildispatcher::factory()
            ->for(Mailgateway::factory()->type(LocalType::class, [])->domain('example.com'), 'gateway')
            ->filter(FilterScope::from([]))
            ->create();
        Member::factory()->defaults()->create(['email' => 'to@example.com']);
        ResyncAction::run();

        $response = $this->patchJson("/maildispatcher/{$dispatcher->id}", [
            'name' => 'testa',
            'gateway_id' => $dispatcher->gateway->id,
            'filter' => [],
        ]);

        $this->assertDatabaseMissing('localmaildispatchers', [
            'from' => 'test@example.com',
        ]);
        $this->assertDatabaseHas('localmaildispatchers', [
            'from' => 'testa@example.com',
            'to' => 'to@example.com',
        ]);
    }

    public function testItDeletesOldEmailAddresses(): void
    {
        $dispatcher = Maildispatcher::factory()
            ->for(Mailgateway::factory()->type(LocalType::class, [])->domain('example.com'), 'gateway')
            ->filter(FilterScope::from([]))
            ->create();
        $member = Member::factory()->defaults()->create(['email' => 'to@example.com']);
        ResyncAction::run();
        $member->update(['email' => 'to2@example.com']);
        ResyncAction::run();

        $this->assertDatabaseMissing('localmaildispatchers', [
            'to' => 'to@example.com',
        ]);
        $this->assertDatabaseHas('localmaildispatchers', [
            'to' => 'to2@example.com',
        ]);
    }
}
