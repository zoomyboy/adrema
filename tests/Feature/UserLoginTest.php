<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testItCanLoginWithUserAccount(): void
    {
        $this->init();
        $this->withoutExceptionHandling();
        $user = User::factory()->create(['email' => 'mail@example.com', 'password' => Hash::make('secret')]);

        $this->post('/login', [
            'email' => 'mail@example.com',
            'password' => 'secret',
        ]);

        $this->assertAuthenticated();
    }

    public function testItThrowsExceptionWhenLoginFailed(): void
    {
        $this->init();
        $user = User::factory()->create(['email' => 'mail@example.com', 'password' => Hash::make('secret')]);

        $this->post('/login', [
            'email' => 'mail@example.com',
            'password' => 'wrong',
        ])->assertRedirect('/');

        $this->assertFalse(auth()->check());
    }
}
