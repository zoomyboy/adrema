<?php

namespace Tests\Feature\Settings;

use App\Setting\GeneralSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GlobalSettingTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->withNamiSettings();
    }

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
