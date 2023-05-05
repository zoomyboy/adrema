<?php

namespace Tests\Feature\Initializer;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;

class ValidateLoginTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testItValidatesLogin(): void
    {
        Auth::success(333, 'secret');

        $this->postJson('/nami-login-check', [
            'mglnr' => 333,
            'password' => 'secret',
        ])->assertStatus(204);
    }

    public function testItNeedsPasswordAndMglnr(): void
    {
        $this->postJson('/nami-login-check', [
            'mglnr' => '',
            'password' => '',
        ])->assertJsonValidationErrors(['mglnr', 'password']);
    }

    public function testMglnrShouldBeNumeric(): void
    {
        $this->postJson('/nami-login-check', [
            'mglnr' => 'aaa',
            'password' => 'secret',
        ])->assertJsonValidationErrors(['mglnr']);
    }

    public function testLoginCanFail(): void
    {
        $this->postJson('/nami-login-check', [
            'mglnr' => '111',
            'password' => 'secret',
        ])->assertJsonValidationErrors(['nami' => 'NaMi Login fehlgeschlagen.']);
    }
}
