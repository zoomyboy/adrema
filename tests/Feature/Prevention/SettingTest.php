<?php

namespace Tests\Feature\Prevention;

use App\Prevention\PreventionSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\EditorRequestFactory;

uses(DatabaseTransactions::class);

it('testItOpensSettingsPage', function () {
    test()->withoutExceptionHandling();
    test()->login()->loginNami();

    test()->get('/setting/prevention')->assertComponent('setting/Prevention')->assertOk();
});

it('receives settings', function () {
    test()->login()->loginNami();

    $text = EditorRequestFactory::new()->text(50, 'lorem ipsum')->toData();
    $yearlyMail = EditorRequestFactory::new()->text(50, 'lala dd')->toData();
    app(PreventionSettings::class)->fill(['formmail' => $text, 'yearlymail' => $yearlyMail, 'weeks' => 9, 'freshRememberInterval' => 11, 'active' => true])->save();

    test()->get('/api/prevention')
        ->assertJsonPath('data.formmail.blocks.0.data.text', 'lorem ipsum')
        ->assertJsonPath('data.yearlymail.blocks.0.data.text', 'lala dd')
        ->assertJsonPath('data.weeks', '9')
        ->assertJsonPath('data.active', true)
        ->assertJsonPath('data.freshRememberInterval', '11');
});

it('testItStoresSettings', function () {
    test()->login()->loginNami();

    $formmail = EditorRequestFactory::new()->text(50, 'new lorem')->create();
    $yearlyMail = EditorRequestFactory::new()->text(50, 'lala dd')->create();
    test()->post('/api/prevention', ['formmail' => $formmail, 'yearlymail' => $yearlyMail, 'weeks' => 9, 'freshRememberInterval' => 11, 'active' => true])->assertOk();
    test()->assertTrue(app(PreventionSettings::class)->formmail->hasAll(['new lorem']));
    test()->assertTrue(app(PreventionSettings::class)->yearlymail->hasAll(['lala dd']));
    test()->assertEquals(9, app(PreventionSettings::class)->weeks);
    test()->assertEquals(11, app(PreventionSettings::class)->freshRememberInterval);
    test()->assertTrue(app(PreventionSettings::class)->active);
});
