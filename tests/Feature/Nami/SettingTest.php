<?php

namespace Tests\Feature\Nami;

use App\Invoice\InvoiceSettings;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;
use Zoomyboy\LaravelNami\Nami;

uses(DatabaseTransactions::class);

it('testItDisplaysView', function () {
    $this->withoutExceptionHandling()->login()->loginNami();

    $this->get(route('setting.view', ['settingGroup' => 'nami']))
        ->assertOk()
        ->assertComponent('setting/Nami');
});

it('testDisplaySettings', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    app(NamiSettings::class)->fill([
        'mglnr' => '0111',
        'password' => 'secret',
        'default_group_id' => '12345',
        'search_params' => [],
    ])->save();

    $this->get(route('setting.data', ['settingGroup' => 'nami']))
        ->assertOk()
        ->assertComponent('setting/Nami')
        ->assertInertiaPath('data.mglnr', '0111')
        ->assertInertiaPath('data.password', '')
        ->assertInertiaPath('data.default_group_id', 12345);
});

it('testItCanChangeSettings', function () {
    $this->login()->loginNami();
    Auth::success(90100, 'secret');

    $response = $this->from('/setting/nami')->post('/setting/nami', [
        'mglnr' => 90100,
        'password' => 'secret',
        'default_group_id' => '12345',
        'search_params' => [],
    ]);

    $response->assertRedirect('/setting/nami');
    $settings = app(NamiSettings::class);
    $this->assertEquals(90100, $settings->mglnr);
    $this->assertEquals('secret', $settings->password);
    $this->assertEquals('12345', $settings->default_group_id);
});

it('validates settings', function () {
    $this->login()->loginNami();

    $this->from('/setting/nami')->post('/setting/nami', [
        'mglnr' => 90100,
        'password' => 'fdsfsdfdsf',
        'default_group_id' => '12345',
        'search_params' => [],
    ])->assertSessionHasErrors(['nami' => 'NaMi Login fehlgeschlagen.']);
});

it('can set mglnr to a string', function () {
    $this->login()->loginNami();
    Auth::success('090100', 'secret');

    $response = $this->from('/setting/nami')->post('/setting/nami', [
        'mglnr' => '090100',
        'password' => 'secret',
        'default_group_id' => '12345',
        'search_params' => [],
    ]);

    $response->assertRedirect('/setting/nami');
    $settings = app(NamiSettings::class);
    $this->assertSame('090100', $settings->mglnr);
    $this->assertEquals('secret', $settings->password);
    $this->assertEquals('12345', $settings->default_group_id);
});
