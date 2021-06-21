<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class LoginTest extends TestCase
{

    public function test_it_stores_login_cookie()
    {
        $this->withoutExceptionHandling();
        $this->fakeNamiMembers([
            [ 'gruppierungId' => 11222, 'vorname' => 'Max', 'nachname' => 'Muster', 'id' => 123 ]
        ]);
        $this->fakeNamiPassword(123, 'secret', [11222]);

        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret'
        ]);

        $cache = Cache::get('namicookie-123');

        $key = session()->get('auth_key');
        $cache = Cache::get("namiauth-{$key}");
        $this->assertEquals('secret', data_get($cache, 'credentials.password'));
        $this->assertEquals(123, auth()->user()->getMglnr());
        $this->assertEquals('Max', auth()->user()->getFirstname());
        $this->assertEquals('Muster', auth()->user()->getLastname());
        $this->assertTrue(auth()->check());

    }
}
