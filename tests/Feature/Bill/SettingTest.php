<?php

namespace Tests\Feature\Bill;

use App\Letter\LetterSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseTransactions;

    public function testSettingIndex(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        LetterSettings::fake([
            'from_long' => 'DPSG Stamm Muster',
            'from' => 'Stamm Muster',
            'mobile' => '+49 176 55555',
            'email' => 'max@muster.de',
            'website' => 'https://example.com',
            'address' => 'Musterstr 4',
            'place' => 'Solingen',
            'zip' => '12345',
        ]);

        $response = $this->get('/setting/bill');

        $response->assertOk();
        $this->assertInertiaHas([
            'from_long' => 'DPSG Stamm Muster',
            'from' => 'Stamm Muster',
            'mobile' => '+49 176 55555',
            'email' => 'max@muster.de',
            'website' => 'https://example.com',
            'address' => 'Musterstr 4',
            'place' => 'Solingen',
            'zip' => '12345',
        ], $response, 'data');
    }

    public function testItReturnsTabs(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        $response = $this->get('/setting/bill');

        /** @var array<int, array{url: string, is_active: bool}> */
        $menus = $this->inertia($response, 'setting_menu');
        $this->assertTrue(
            collect($menus)
                ->pluck('url')
                ->contains('/setting/bill')
        );

        $settingMenu = collect($menus)->first(fn ($menu) => '/setting/bill' === $menu['url']);
        $this->assertTrue($settingMenu['is_active']);
        $this->assertEquals('Rechnung', $settingMenu['title']);
    }

    public function testItCanChangeSettings(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        $response = $this->from('/setting/bill')->post('/setting/bill', [
            'from_long' => 'DPSG Stamm Muster',
            'from' => 'Stamm Muster',
            'mobile' => '+49 176 55555',
            'email' => 'max@muster.de',
            'website' => 'https://example.com',
            'address' => 'Musterstr 4',
            'place' => 'Solingen',
            'zip' => '12345',
        ]);

        $response->assertRedirect('/setting/bill');
        $settings = app(LetterSettings::class);
        $this->assertEquals('DPSG Stamm Muster', $settings->from_long);
    }
}
