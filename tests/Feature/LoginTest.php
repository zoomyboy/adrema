<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_it_stores_login_cookie()
    {
        $this->withoutExceptionHandling();
        $this->fakeNamiMembers([
            [ 'gruppierungId' => 11222, 'vorname' => 'Max', 'id' => 123 ]
        ]);
        $this->fakeNamiPassword(123, 'secret', [11222]);

        $this->post('/login', [
            'groupid' => 11222,
            'mglnr' => 123,
            'password' => 'secret'
        ]);

        $cache = Cache::get('namicookie-123');
        $this->assertEquals('JSESSIONID', data_get($cache, 'cookie.0.Name'));
        $this->assertEquals('123', data_get($cache, 'data.mitgliedsnr'));
    }
}
