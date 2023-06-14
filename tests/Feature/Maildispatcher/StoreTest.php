<?php

namespace Tests\Feature\Maildispatcher;

use App\Activity;
use App\Maildispatcher\Models\Maildispatcher;
use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Types\LocalType;
use App\Member\Member;
use App\Member\Membership;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login()->loginNami();
    }

    public function testItCanStoreAMail(): void
    {
        $gateway = Mailgateway::factory()->type(LocalType::class, [])->domain('example.com')->create();
        Member::factory()->defaults()->create();
        Member::factory()->defaults()->has(Membership::factory()->inLocal('Leiter*in', 'Wölfling'))->create(['email' => 'jane@example.com']);
        $activityId = Activity::first()->id;

        $response = $this->postJson('/maildispatcher', [
            'name' => 'test',
            'gateway_id' => $gateway->id,
            'filter' => ['activity_ids' => [$activityId]],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('maildispatchers', [
            'name' => 'test',
            'gateway_id' => $gateway->id,
            'filter' => "{\"activity_ids\":[{$activityId}]}",
        ]);
        $dispatcher = Maildispatcher::first();
        $this->assertDatabaseCount('localmaildispatchers', 1);
        $this->assertDatabaseHas('localmaildispatchers', [
            'from' => 'test@example.com',
            'to' => 'jane@example.com',
        ]);
    }
}
