<?php

namespace Tests\Feature\Prevention;

use App\Prevention\PreventionSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\EditorRequestFactory;
use Tests\TestCase;

class SettingTest extends TestCase
{

    use DatabaseTransactions;

    public function testItOpensSettingsPage(): void
    {
        $this->login()->loginNami();

        $this->get('/setting/prevention')->assertComponent('prevention/Index')->assertOk();
    }

    public function testItReceivesSettings(): void
    {
        $this->login()->loginNami();

        $text = EditorRequestFactory::new()->text(50, 'lorem ipsum')->create();
        app(PreventionSettings::class)->fill(['formmail' => $text])->save();

        $this->get('/api/prevention')
            ->assertJsonPath('data.formmail.blocks.0.data.text', 'lorem ipsum');
    }

    public function testItStoresSettings(): void
    {
        $this->login()->loginNami();

        $text = EditorRequestFactory::new()->text(50, 'new lorem')->create();

        $this->post('/api/prevention', ['formmail' => $text])->assertOk();
        $this->assertEquals($text, app(PreventionSettings::class)->formmail);
    }
}
