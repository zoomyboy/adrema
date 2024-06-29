<?php

namespace Tests\Feature\Group;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\Fileshare;
use App\Group;
use App\Group\Enums\Level;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BulkstoreTest extends TestCase
{
    use DatabaseTransactions;

    public function testItSavesGroupsLevelAndParent(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $group = Group::factory()->for(Group::first(), 'parent')->create(['inner_name' => 'Gruppe', 'level' => Level::REGION]);

        $this->postJson(route('group.bulkstore'), [
            ['id' => $group->id, 'inner_name' => 'Abc', 'level' => Level::FEDERATION->value, 'fileshare' => null]
        ])->assertOk();

        $this->assertNull($group->fresh()->fileshare);
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'inner_name' => 'Abc',
            'level' => 'Diözese',
            'fileshare' => null,
        ]);
    }

    public function testItStoresFileconnection(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->name('lokaler Server')
            ->create();

        $group = Group::factory()->for(Group::first(), 'parent')->create(['inner_name' => 'Gruppe', 'level' => Level::REGION]);

        $this->postJson(route('group.bulkstore'), [
            ['id' => $group->id, 'inner_name' => 'Abc', 'level' => Level::FEDERATION->value, 'fileshare' => [
                'connection_id' => $connection->id,
                'resource' => '/abc',
            ]]
        ])->assertOk();

        $this->assertEquals($connection->id, $group->fresh()->fileshare->connectionId);
        $this->assertEquals('/abc', $group->fresh()->fileshare->resource);
    }
}
