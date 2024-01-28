<?php

namespace Tests\EndToEnd\Maildispatcher;

use \Mockery as M;
use App\Activity;
use App\Maildispatcher\Models\Localmaildispatcher;
use App\Maildispatcher\Models\Maildispatcher;
use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Types\LocalType;
use App\Member\Member;
use App\Member\Membership;
use Tests\EndToEndTestCase;

class StoreTest extends EndToEndTestCase
{

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

        sleep(1);
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

    public function testItDoesntStoreTwoMembersWithSameEmailAddress(): void
    {
        $type = M::mock(LocalType::class)->makePartial();
        $type->shouldReceive('add')->once();
        app()->instance(LocalType::class, $type);

        $gateway = Mailgateway::factory()->type(LocalType::class, [])->domain('example.com')->create();
        Member::factory()->defaults()->create(['email' => 'jane@example.com']);
        Member::factory()->defaults()->create(['email' => 'jane@example.com']);

        sleep(1);
        $this->postJson('/maildispatcher', [
            'name' => 'test',
            'gateway_id' => $gateway->id,
            'filter' => [],
        ]);
    }

    public function testMaildispatcherReceivesLowerVersionOfEmail(): void
    {
        $gateway = Mailgateway::factory()->type(LocalType::class, [])->create();
        Member::factory()->defaults()->create(['email' => 'Jane@example.com']);

        sleep(1);
        $this->postJson('/maildispatcher', [
            'name' => 'test',
            'gateway_id' => $gateway->id,
            'filter' => [],
        ]);

        $this->assertEquals('jane@example.com', Localmaildispatcher::first()->to);
    }
}
