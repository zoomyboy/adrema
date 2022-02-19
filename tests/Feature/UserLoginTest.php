<?php

namespace Tests\Feature;

use App\Setting\GeneralSettings;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Backend\FakeBackend;

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
            'provider' => 'database',
            'password' => 'secret'
        ]);

        $key = session()->get('auth_key');
        $cache = Cache::get("namiauth-{$key}");
        $this->assertEquals($user->id, data_get($cache, 'id'));
        $this->assertTrue(auth()->check());
    }

    public function testItThrowsExceptionWhenLoginFailed(): void
    {
        $this->init();
        $user = User::factory()->create(['email' => 'mail@example.com', 'password' => Hash::make('secret')]);

        $this->post('/login', [
            'email' => 'mail@example.com',
            'provider' => 'database',
            'password' => 'wrong'
        ])->assertRedirect('/');

        $this->assertFalse(auth()->check());
    }

}
