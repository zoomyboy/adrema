<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Tests\Lib\InertiaMixin;
use Zoomyboy\LaravelNami\Backend\FakeBackend;
use Zoomyboy\LaravelNami\FakesNami;
use Zoomyboy\LaravelNami\Nami;
use Zoomyboy\LaravelNami\NamiUser;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use FakesNami;

    public function setUp(): void {
        parent::setUp();
    
        $this->fakeNami();
        TestResponse::mixin(new InertiaMixin());
    }

    public function login(): void
    {
        app(FakeBackend::class)
            ->fakeLogin('123')
            ->addSearch(123, ['entries_vorname' => '::firstname::', 'entries_nachname' => '::lastname::', 'entries_gruppierungId' => 1000]);
        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret',
        ]);
    }

}
