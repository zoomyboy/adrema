<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Tests\Lib\InertiaMixin;
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

    public function login() {
        $this->fakeNamiMembers([
            [ 'gruppierungId' => 12399, 'vorname' => 'Max', 'nachname' => '::lastname::', 'id' => 999 ]
        ]);

        $this->fakeNamiPassword(999, 'secret', [12399]);
        $api = Nami::login(999, 'secret');

        $this->be(NamiUser::fromPayload([
            'credentials' => [
                'mglnr' => 999,
                'password' => 'secret'
            ]
        ]));

    }

}
