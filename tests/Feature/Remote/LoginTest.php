<?php

namespace Tests\Feature\Nami;

use Illuminate\Support\Facades\Crypt;
use Tests\EndToEndTestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;

class LoginTest extends EndToEndTestCase
{
    public function testItCanLoginRemotelyWithUser(): void
    {
        Auth::success(90100, 'secret');
        $response = $this->postJson(route('remote.nami.token'), [
            'mglnr' => 90100,
            'password' => 'secret',
        ]);
        $response->assertOk();

        $accessTokenPayload = Crypt::decryptString($response->json('access_token'));
        $this->assertEquals(['mglnr' => 90100, 'password' => 'secret'], json_decode($accessTokenPayload, true));
    }
}
