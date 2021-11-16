<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoginTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testItCanLoginWithANamiAccount()
    {
        $this->withoutExceptionHandling();
        app('nami.backend')->fakeLogin(123, [], 'cookie-123');

        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret'
        ]);

        $key = session()->get('auth_key');
        $cache = Cache::get("namiauth-{$key}");
        $this->assertEquals('secret', data_get($cache, 'credentials.password'));
        $this->assertEquals(123, data_get($cache, 'credentials.mglnr'));
        $this->assertTrue(auth()->check());
    }

    public function testItDoesntLoginTwoTimes()
    {
        $this->withoutExceptionHandling();
        app('nami.backend')->fakeLogin(123, [], 'cookie-123');

        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret'
        ]);
        auth()->logout();
        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret'
        ]);

        $this->assertTrue(auth()->check());

        Http::assertSentCount(2);
    }

    public function testItThrowsExceptionWhenLoginFailed()
    {
        app('nami.backend')->fakeFailedLogin(123);

        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret'
        ])->assertRedirect('/');

        $this->assertFalse(auth()->check());

        Http::assertSentCount(2);
    }

}
