<?php

namespace Tests\Feature\Setting;

use App\Setting\BillSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseTransactions;

    public function testSettingIndex(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        BillSettings::fake([
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
        $settings = app(BillSettings::class);
        $this->assertEquals('DPSG Stamm Muster', $settings->from_long);
    }
}
