<?php

namespace Tests\Feature\Group;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\Fileshare;
use App\Group;
use App\Group\Enums\Level;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysGroupPage(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $this->get('/group')
            ->assertOk()
            ->assertInertiaPath('data.meta.links.root_path', route('api.group'));
    }

    public function testItDisplaysParentGroup(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $group = Group::factory()->create(['name' => 'Afff', 'inner_name' => 'Gruppe', 'level' => Level::REGION]);

        $this->get('/api/group')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.1.name', 'Afff')
            ->assertJsonPath('data.1.inner_name', 'Gruppe')
            ->assertJsonPath('data.1.id', $group->id)
            ->assertJsonPath('data.1.level', 'Bezirk')
            ->assertJsonPath('data.1.parent_id', null)
            ->assertJsonPath('meta.links.bulkstore', route('group.bulkstore'));
    }

    public function testItPrefersInnerName(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $group = Group::factory()->create(['name' => 'Afff', 'inner_name' => 'Gruppe', 'level' => Level::REGION]);

        $this->get('/api/group?prefer_inner')
            ->assertJsonPath('data.1.name', 'Gruppe')
            ->assertJsonPath('data.1.id', $group->id);
    }

    public function testItDisplaysGroupsForParent(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $group = Group::factory()->for(Group::first(), 'parent')->create(['name' => 'Afff', 'inner_name' => 'Gruppe', 'level' => Level::REGION]);
        Group::factory()->for(Group::first(), 'parent')->create(['name' => 'Afff', 'inner_name' => 'Gruppe', 'level' => Level::REGION]);

        $this->get('/api/group/' . Group::first()->id)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Afff')
            ->assertJsonPath('data.0.inner_name', 'Gruppe')
            ->assertJsonPath('data.0.id', $group->id)
            ->assertJsonPath('data.0.level', 'Bezirk')
            ->assertJsonPath('data.0.parent_id', Group::first()->id)
            ->assertJsonPath('data.0.links.children', route('api.group', ['group' => $group->id]))
            ->assertJsonPath('meta.links.bulkstore', route('group.bulkstore'));
    }

    public function testLevelCanBeNull(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $group = Group::factory()->for(Group::first(), 'parent')->create(['level' => null]);

        $this->get('/api/group/' . Group::first()->id)->assertJsonPath('data.0.id', $group->id);
    }

    public function testItDisplaysFileshare(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->name('lokaler Server')
            ->create();

        Group::factory()->for(Group::first(), 'parent')->create(['level' => null, 'fileshare' => [
            'connection_id' => $connection->id,
            'resource' => '/abc',
        ]]);

        $this->get('/api/group/' . Group::first()->id)->assertJsonPath('data.0.fileshare.resource', '/abc');
    }
}
