<?php

namespace Tests\Unit;

use App\Group as GroupModel;
use App\Initialize\InitializeGroups;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Group;

class InitializeGroupsTest extends TestCase
{

    use DatabaseTransactions;

    private Api $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->createStub(Api::class);
    }

    public function test_it_doesnt_initialize_groups_when_nothing_sent(): void
    {
        $this->api->method('groups')->willReturn(collect([]));

        $task = new InitializeGroups($this->api);
        $task->handle();

        $this->assertDatabaseCount('groups', 0);
    }

    public function test_it_synchs_a_group_with_a_single_node_and_no_children(): void
    {
        $this->api->method('groups')->will($this->returnValueMap([
            [
                null,
                collect([(new Group())->setParentId(null)->setId(150)->setName('lorem')])
            ]
        ]));
        $this->api->method('subgroupsOf')->willReturn(collect([]));

        (new InitializeGroups($this->api))->handle();

        $this->assertDatabaseHas('groups', [
            'nami_id' => 150,
            'name' => 'lorem',
            'parent_id' => null
        ]);
    }

    public function testItDoesntCreateAGroupTwiceWithTheSameNamiId(): void
    {
        GroupModel::factory()->create(['nami_id' => 150]);
        $this->api->method('groups')->will($this->returnValueMap([
            [
                null,
                collect([(new Group())->setParentId(null)->setId(150)->setName('lorem')])
            ]
        ]));
        $this->api->method('subgroupsOf')->willReturn(collect([]));

        (new InitializeGroups($this->api))->handle();

        $this->assertDatabaseCount('groups', 1);
    }

    public function testItSynchsSubgroups(): void
    {
        GroupModel::factory()->create(['nami_id' => 150]);
        $this->api->method('groups')->willReturn(
            collect([(new Group())->setParentId(null)->setId(150)->setName('lorem')])
        );
        $this->api->method('subgroupsOf')->willReturn(
                collect([(new Group())->setParentId(150)->setId(200)->setName('subgroup')])
        );

        (new InitializeGroups($this->api))->handle();

        $this->assertDatabaseCount('groups', 2);
        $subgroup = GroupModel::where('nami_id', 200)->firstOrFail();
        $this->assertEquals('subgroup', $subgroup->name);
        $this->assertEquals(150, $subgroup->parent->nami_id);
    }

}
