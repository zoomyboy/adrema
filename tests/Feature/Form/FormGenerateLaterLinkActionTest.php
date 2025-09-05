<?php

namespace Tests\Feature\Form;

use App\Form\FormSettings;
use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->setUpForm();
    Mail::fake();
});

it('generates a later link', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    app(FormSettings::class)->fill(['registerUrl' => 'https://example.com/register/{slug}'])->save();
    $form = Form::factory()->name('fff')->create();

    $url = $this->get(route('form.laterlink', ['form' => $form]))->json('url');
    test()->assertNotNull($url);
    $this->assertTrue(str($url)->startsWith('https://example.com/register/fff'));

    $query = data_get(parse_url($url), 'query');
    parse_str($query, $queryParts);
    $this->assertEquals('1', $queryParts['later']);

    $this->assertEquals($form->id, Cache::get('later_'.$queryParts['id']));
});

