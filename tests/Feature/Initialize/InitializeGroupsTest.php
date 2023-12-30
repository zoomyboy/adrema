<?php

namespace Tests\Feature\Initialize;

use App\Initialize\InitializeGroups;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\GroupFake;

class InitializeGroupsTest extends TestCase
{
    use DatabaseTransactions;

    public function testItInitializesGroups(): void
    {
        app(GroupFake::class)
            ->fetches(null, [1000 => ['name' => 'testgroup']])
            ->fetches(1000, []);

        $this->withoutExceptionHandling()->login()->loginNami();

        (new InitializeGroups(app(NamiSettings::class)->login()))->handle();

        $this->assertDatabaseHas('groups', ['nami_id' => 1000, 'name' => 'testgroup']);
    }

    public function testItInitializesSubgroups(): void
    {
        app(GroupFake::class)
            ->fetches(null, [1000 => ['name' => 'testgroup']])
            ->fetches(1000, [
                1001 => ['name' => 'subgroup1'],
                1002 => ['name' => 'subgroup2'],
            ])
            ->fetches(1001, [])
            ->fetches(1002, []);

        $this->withoutExceptionHandling()->login()->loginNami();

        (new InitializeGroups(app(NamiSettings::class)->login()))->handle();

        $this->assertDatabaseHas('groups', ['nami_id' => 1000, 'name' => 'testgroup', 'inner_name' => 'testgroup', 'level' => null]);
        $this->assertDatabaseHas('groups', ['nami_id' => 1001, 'name' => 'subgroup1']);
        $this->assertDatabaseHas('groups', ['nami_id' => 1002, 'name' => 'subgroup2']);
    }
}
