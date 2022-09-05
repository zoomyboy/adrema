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

        $response = $this->get('/setting');

        $response->assertOk();
        $this->assertInertiaHas([
            'bill_from_long' => 'DPSG Stamm Muster',
            'bill_from' => 'Stamm Muster',
            'bill_mobile' => '+49 176 55555',
            'bill_email' => 'max@muster.de',
            'bill_website' => 'https://example.com',
            'bill_address' => 'Musterstr 4',
            'bill_place' => 'Solingen',
            'bill_zip' => '12345',
        ], $response, 'data');
    }

    public function testItCanChangeSettings(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        $response = $this->from('/setting')->post('/setting', [
            'bill_from_long' => 'DPSG Stamm Muster',
            'bill_from' => 'Stamm Muster',
            'bill_mobile' => '+49 176 55555',
            'bill_email' => 'max@muster.de',
            'bill_website' => 'https://example.com',
            'bill_address' => 'Musterstr 4',
            'bill_place' => 'Solingen',
            'bill_zip' => '12345',
        ]);

        $response->assertRedirect('/setting');
        $settings = app(BillSettings::class);
        $this->assertEquals('DPSG Stamm Muster', $settings->from_long);
    }
}
