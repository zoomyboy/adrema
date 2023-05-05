<?php

namespace Tests\Feature\Initialize;

use App\Initialize\Actions\InitializeAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\InitializeRequestFactory;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;
use Zoomyboy\LaravelNami\Fakes\GroupFake;

class InitializeActionTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCannotInitializeWhenNotLoggedIn(): void
    {
        InitializeAction::partialMock()->shouldReceive('handle')->never();

        $response = $this->post('/initialize', $this->factory()->create());

        $response->assertRedirect('/login');
    }

    public function testItValidatesSetupInfo(): void
    {
        $this->login();

        $response = $this->post('/initialize', $this->factory()->invalid()->create());

        $this->assertErrors(['password' => 'Passwort ist erforderlich.'], $response);
        $this->assertErrors(['mglnr' => 'Mitgliedsnummer ist erforderlich.'], $response);
        $this->assertErrors(['group_id' => 'Gruppierungsnr ist erforderlich.'], $response);
    }

    public function testItValidatesLogin(): void
    {
        $this->login();

        $response = $this->post('/initialize', $this->factory()->withCredentials(12345, 'secret')->create());

        $this->assertErrors(['nami' => 'NaMi Login fehlgeschlagen.'], $response);
    }

    public function testItValidatesGroupExistance(): void
    {
        $this->login();
        Auth::success(12345, 'secret');
        app(GroupFake::class)->fetches(null, []);

        $response = $this->post('/initialize', $this->factory()->withCredentials(12345, 'secret')->withGroup(185)->create());

        $this->assertErrors(['nami' => 'Gruppierung nicht gefunden.'], $response);
    }

    private function factory(): InitializeRequestFactory
    {
        return InitializeRequestFactory::new();
    }
}
