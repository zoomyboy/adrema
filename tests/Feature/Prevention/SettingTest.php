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
    app(PreventionSettings::class)->fill(['formmail' => $text, 'yearlymail' => $yearlyMail])->save();

    test()->get('/api/prevention')
        ->assertJsonPath('data.formmail.blocks.0.data.text', 'lorem ipsum')
        ->assertJsonPath('data.yearlymail.blocks.0.data.text', 'lala dd');
});

it('testItStoresSettings', function () {
    test()->login()->loginNami();

    $formmail = EditorRequestFactory::new()->text(50, 'new lorem')->create();
    $yearlyMail = EditorRequestFactory::new()->text(50, 'lala dd')->create();
    test()->post('/api/prevention', ['formmail' => $formmail, 'yearlymail' => $yearlyMail])->assertOk();
    test()->assertTrue(app(PreventionSettings::class)->formmail->hasAll(['new lorem']));
    test()->assertTrue(app(PreventionSettings::class)->yearlymail->hasAll(['lala dd']));
});
