<?php

namespace Tests\Feature\Invoice;

use App\Invoice\InvoiceSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseTransactions;

    public function testDisplaySettings(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        app(InvoiceSettings::class)->fill([
            'from_long' => 'DPSG Stamm Muster',
            'from' => 'Stamm Muster',
            'mobile' => '+49 176 55555',
            'email' => 'max@muster.de',
            'website' => 'https://example.com',
            'address' => 'Musterstr 4',
            'place' => 'Solingen',
            'zip' => '12345',
            'iban' => 'DE05',
            'bic' => 'SOLSDE',
            'rememberWeeks' => 6
        ])->save();

        $this->get('/setting/bill')
            ->assertOk()
            ->assertComponent('setting/Bill')
            ->assertInertiaPath('data.from_long', 'DPSG Stamm Muster')
            ->assertInertiaPath('data.from', 'Stamm Muster')
            ->assertInertiaPath('data.mobile', '+49 176 55555')
            ->assertInertiaPath('data.email', 'max@muster.de')
            ->assertInertiaPath('data.website', 'https://example.com')
            ->assertInertiaPath('data.address', 'Musterstr 4')
            ->assertInertiaPath('data.place', 'Solingen')
            ->assertInertiaPath('data.zip', '12345')
            ->assertInertiaPath('data.iban', 'DE05')
            ->assertInertiaPath('data.bic', 'SOLSDE')
            ->assertInertiaPath('data.remember_weeks', 6);
    }

    public function testItReturnsTabs(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        $this->get('/setting/bill')
            ->assertInertiaPath('setting_menu.1.title', 'Rechnung')
            ->assertInertiaPath('setting_menu.1.url', '/setting/bill')
            ->assertInertiaPath('setting_menu.1.is_active', true)
            ->assertInertiaPath('setting_menu.0.is_active', false);
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
            'iban' => 'DE05',
            'bic' => 'SOLSDE',
            'remember_weeks' => 10
        ]);

        $response->assertRedirect('/setting/bill');
        $settings = app(InvoiceSettings::class);
        $this->assertEquals('DPSG Stamm Muster', $settings->from_long);
        $this->assertEquals('DE05', $settings->iban);
        $this->assertEquals('SOLSDE', $settings->bic);
        $this->assertEquals(10, $settings->rememberWeeks);
    }
}
