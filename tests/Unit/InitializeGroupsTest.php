<?php

namespace Tests\Unit;

use App\Group as GroupModel;
use App\Initialize\InitializeGroups;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\MockObject\Stub;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\Group;

class InitializeGroupsTest extends TestCase
{
    use DatabaseTransactions;

    private Stub $api;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->createStub(Api::class);
    }

    public function testItDoesntInitializeGroupsWhenNothingSent(): void
    {
        $this->api->method('groups')->willReturn(collect([]));

        $task = new InitializeGroups($this->api);
        $task->handle();

        $this->assertDatabaseCount('groups', 0);
    }

    public function testItSynchsAGroupWithASingleNodeAndNoChildren(): void
    {
        $parentGroup = Group::from(['id' => 150, 'name' => 'lorem', 'parentId' => null]);
        $this->api->method('groups')->willReturnMap([
            [null, collect([$parentGroup])],
            [$parentGroup, collect([])],
        ]);
        $this->api->method('groups')->willReturn(collect([]));

        (new InitializeGroups($this->api))->handle();

        $this->assertDatabaseHas('groups', [
            'nami_id' => 150,
            'name' => 'lorem',
            'inner_name' => 'lorem',
            'parent_id' => null,
        ]);
    }

    public function testItDoesntCreateAGroupTwiceWithTheSameNamiId(): void
    {
        $existingGroup = GroupModel::factory()->create(['nami_id' => 150, 'inner_name' => 'Def']);
        $parentGroup = Group::from(['id' => 150, 'name' => 'lorem', 'parentId' => null]);
        $this->api->method('groups')->willReturnMap([
            [null, collect([$parentGroup])],
            [$parentGroup, collect([])],
        ]);

        (new InitializeGroups($this->api))->handle();

        $this->assertDatabaseCount('groups', 1);
        $this->assertDatabaseHas('groups', [
            'id' => $existingGroup->id,
            'name' => 'lorem',
            'inner_name' => 'Def',
            'nami_id' => 150
        ]);
    }

    public function testItSynchsSubgroups(): void
    {
        $parentGroup = Group::from(['id' => 150, 'name' => 'lorem', 'parentId' => null]);
        $subgroup = Group::from(['id' => 200, 'name' => 'subgroup', 'parentId' => 150]);
        $this->api->method('groups')->willReturnMap([
            [null, collect([$parentGroup])],
            [$parentGroup, collect([$subgroup])],
            [$subgroup, collect([])],
        ]);

        (new InitializeGroups($this->api))->handle();

        $this->assertDatabaseCount('groups', 2);
        $subgroup = GroupModel::where('nami_id', 200)->firstOrFail();
        $this->assertEquals('subgroup', $subgroup->name);
        $this->assertEquals(150, $subgroup->parent->nami_id);
    }

    public function testItSynchsSubgroupsOfSubgroups(): void
    {
        $parentGroup = Group::from(['id' => 150, 'name' => 'lorem', 'parentId' => null]);
        $subgroup = Group::from(['id' => 200, 'name' => 'subgroup', 'parentId' => 150]);
        $subsubgroup = Group::from(['id' => 250, 'name' => 'subsubgroup', 'parentId' => 200]);
        $this->api->method('groups')->willReturnMap([
            [null, collect([$parentGroup])],
            [$parentGroup, collect([$subgroup])],
            [$subgroup, collect([$subsubgroup])],
            [$subsubgroup, collect([])],
        ]);

        (new InitializeGroups($this->api))->handle();

        $this->assertDatabaseCount('groups', 3);
    }

    public function testItAssignsIdAndParentToAnExistingSubgroup(): void
    {
        $existingSubgroup = GroupModel::factory()->create(['name' => 'Abc', 'inner_name' => 'Def', 'nami_id' => 200]);
        $parentGroup = Group::from(['id' => 150, 'name' => 'root', 'parentId' => null]);
        $subgroup = Group::from(['id' => 200, 'name' => 'child', 'parentId' => 150]);
        $this->api->method('groups')->willReturnMap([
            [null, collect([$parentGroup])],
            [$parentGroup, collect([$subgroup])],
            [$subgroup, collect([])],
        ]);

        (new InitializeGroups($this->api))->handle();

        $this->assertDatabaseCount('groups', 2);
        $this->assertDatabaseHas('groups', [
            'id' => $existingSubgroup->id,
            'nami_id' => 200,
            'name' => 'child',
            'inner_name' => 'Def',
            'parent_id' => GroupModel::firstWhere('nami_id', 150)->id,
        ]);
    }
}
