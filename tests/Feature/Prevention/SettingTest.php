<?php

namespace Tests\Feature\Prevention;

use App\Member\FilterScope;
use App\Prevention\Enums\Prevention;
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
    app(PreventionSettings::class)->fill([
        'formmail' => $text,
        'yearlymail' => $yearlyMail,
        'weeks' => 9,
        'freshRememberInterval' => 11,
        'active' => true,
        'replyToMail' => 'admin@example.com',
        'preventAgainst' => [Prevention::MOREPS->name],
        'yearlyMemberFilter' => FilterScope::from([
            'memberships' => [['group_ids' => [33]]],
            'search' => 'searchstring',
        ]),
    ])->save();

    test()->get('/api/prevention')
        ->assertJsonPath('data.formmail.blocks.0.data.text', 'lorem ipsum')
        ->assertJsonPath('data.yearlymail.blocks.0.data.text', 'lala dd')
        ->assertJsonPath('data.weeks', '9')
        ->assertJsonPath('data.active', true)
        ->assertJsonPath('data.freshRememberInterval', '11')
        ->assertJsonPath('data.replyToMail', 'admin@example.com')
        ->assertJsonPath('data.yearlyMemberFilter.search', 'searchstring')
        ->assertJsonPath('data.yearlyMemberFilter.memberships.0.group_ids.0', 33)
        ->assertJsonPath('data.preventAgainst', ['MOREPS'])
        ->assertJsonPath('meta.preventAgainsts.0.name', 'erweitertes Führungszeugnis')
        ->assertJsonPath('meta.preventAgainsts.0.id', 'EFZ');
});

it('testItStoresSettings', function () {
    test()->login()->loginNami();

    test()->post('/api/prevention', [
        'formmail' => EditorRequestFactory::new()->text(50, 'new lorem')->create(),
        'yearlymail' => EditorRequestFactory::new()->text(50, 'lala dd')->create(),
        'weeks' => 9,
        'freshRememberInterval' => 11,
        'active' => true,
        'preventAgainst' => ['EFZ'],
        'replyToMail' => 'admin@example.com',
        'yearlyMemberFilter' => [
            'memberships' => [['group_ids' => 33]],
            'search' => 'searchstring',
        ],
    ])->assertOk();
    test()->assertTrue(app(PreventionSettings::class)->formmail->hasAll(['new lorem']));
    test()->assertTrue(app(PreventionSettings::class)->yearlymail->hasAll(['lala dd']));
    test()->assertEquals(9, app(PreventionSettings::class)->weeks);
    test()->assertEquals('admin@example.com', app(PreventionSettings::class)->replyToMail);
    test()->assertEquals(11, app(PreventionSettings::class)->freshRememberInterval);
    test()->assertTrue(app(PreventionSettings::class)->active);
    test()->assertEquals([['group_ids' => 33]], app(PreventionSettings::class)->yearlyMemberFilter->memberships);
    test()->assertEquals('searchstring', app(PreventionSettings::class)->yearlyMemberFilter->search);
    test()->assertEquals('EFZ', app(PreventionSettings::class)->preventAgainst[0]);
});
