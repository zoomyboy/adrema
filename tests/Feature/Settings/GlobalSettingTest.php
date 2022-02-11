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
            'modules' => ['bill'],
        ]);
        $this->withoutExceptionHandling();
        $this->login()->init();

        $response = $this->get('/setting');
                         
        $this->assertComponent('setting/Index', $response);
        $this->assertEquals(['bill'], $this->inertia($response, 'general.modules'));
    }

    public function testItGetsOptionsForModels(): void
    {
        $this->withoutExceptionHandling();
        $this->login()->init();

        $response = $this->get('/setting');
                         
        $this->assertContains('bill', $this->inertia($response, 'options.modules'));
    }

}
