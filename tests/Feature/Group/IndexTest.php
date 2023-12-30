<?php

namespace Tests\Feature\Group;

use App\Group;
use App\Group\Enums\Level;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use DatabaseTransactions;

    public function testItDisplaysAllActivitiesAndSubactivities(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        $group = Group::factory()->for(Group::first(), 'parent')->create(['name' => 'Afff', 'inner_name' => 'Gruppe', 'level' => Level::REGION]);

        $this->get('/group')
            ->assertInertiaPath('data.data.1.name', 'Afff')
            ->assertInertiaPath('data.data.1.inner_name', 'Gruppe')
            ->assertInertiaPath('data.data.1.id', $group->id)
            ->assertInertiaPath('data.data.1.level', 'Bezirk')
            ->assertInertiaPath('data.data.1.parent_id', Group::first()->id)
            ->assertInertiaPath('data.meta.links.bulkstore', route('group.bulkstore'));
    }

    public function testLevelCanBeNull(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();

        Group::factory()->create(['level' => null]);

        $this->get('/group')->assertInertiaPath('data.data.2.level', null);
    }
}
