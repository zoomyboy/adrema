<?php

namespace Tests\Feature\Initialize;

use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function testItRedirectsToInitializeRouteWhenNotInitialized(): void
    {
        $this->login();
        $response = $this->get('/');

        $response->assertRedirect('/initialize');
    }

    public function testItDoesntRedirctIfUserIsGuest(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function testItDoesntRedirectToInitializeRoute(): void
    {
        $this->login();
        $response = $this->get('/initialize');

        $response->assertStatus(200);
    }

    public function testItDoesntRedirectWhenAlreadyInitialized(): void
    {
        NamiSettings::fake([
            'mglnr' => 333,
            'password' => 'secret',
            'default_group_id' => 555,
        ]);
        $this->login();

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testItRedirectsHomeWhenAlreadyInitialized(): void
    {
        NamiSettings::fake([
            'mglnr' => 333,
            'password' => 'secret',
            'default_group_id' => 555,
        ]);
        $this->login();

        $response = $this->get('/initialize');

        $response->assertRedirect('/');
    }
}
