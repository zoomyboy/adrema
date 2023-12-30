<?php

namespace Tests\Feature\Group;

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
            ['id' => $group->id, 'inner_name' => 'Abc', 'level' => Level::FEDERATION->value]
        ])->assertOk();

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'inner_name' => 'Abc',
            'level' => 'Diözese',
        ]);
    }
}
