<?php

namespace Tests\Feature\Settings;

use App\Setting\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GlobalSettingTest extends TestCase
{

    use RefreshDatabase;

    public function testItLoadsGeneralSettings(): void
    {
        GeneralSettings::fake([
            'mode' => ['bill']
        ]);
        $this->withoutExceptionHandling();
        $this->login()->init();

        $response = $this->get('/setting');
                         
        $response->assertInertiaComponent('setting/Index');
        $this->assertEquals(['bill'], $response->inertia('general.modes'));
    }

    public function testItGetsOptionsForModels(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->init();

        $response = $this->get('/setting');
                         
        $this->assertContains('bill', $response->inertia('options.modes'));
    }

}
