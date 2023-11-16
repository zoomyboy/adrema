<?php

namespace Tests\Feature;

use App\Module\Module;
use App\Module\ModuleSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModuleTest extends TestCase
{

    use DatabaseTransactions;

    public function testItGetsModuleSettings(): void
    {
        $this->login()->loginNami();
        ModuleSettings::fake(['modules' => ['bill']]);

        $response = $this->get('/setting/module');

        $response->assertOk();
        $this->assertCount(count(Module::cases()), $this->inertia($response, 'data.meta.modules'));
        $this->assertInertiaHas([
            'name' => 'Zahlungs-Management',
            'id' => 'bill',
        ], $response, 'data.meta.modules.0');
        $this->assertEquals(['bill'], $this->inertia($response, 'data.data.modules'));
    }

    public function testItSavesSettings(): void
    {
        $this->login()->loginNami();

        $response = $this->from('/setting/module')->post('/setting/module', [
            'modules' => ['bill'],
        ]);

        $response->assertRedirect('/setting/module');
        $this->assertEquals(['bill'], app(ModuleSettings::class)->modules);
    }

    public function testModuleMustExists(): void
    {
        $this->login()->loginNami();

        $response = $this->from('/setting/module')->post('/setting/module', [
            'modules' => ['lalala'],
        ]);

        $response->assertSessionHasErrors('modules.0');
    }

    public function testItReturnsModulesOnEveryPage(): void
    {
        $this->login()->loginNami();
        ModuleSettings::fake(['modules' => ['bill']]);

        $response = $this->get('/');

        $this->assertEquals(['bill'], $this->inertia($response, 'settings.modules'));
    }
}
