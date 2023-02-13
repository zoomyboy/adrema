<?php

namespace Tests\Feature\Initialize;

use App\Initialize\Actions\InitializeAction;
use App\Setting\NamiSettings;
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

    public function testItSetsSettingsBeforeRunningInitializer(): void
    {
        $this->withoutExceptionHandling()->login();
        InitializeAction::partialMock()->shouldReceive('handle')->with(12345, 'secret', 185)->once()->andReturn(true);
        Auth::success(12345, 'secret');
        app(GroupFake::class)->fetches(null, [185 => ['name' => 'testgroup']]);

        $response = $this->post('/initialize', $this->factory()->withCredentials(12345, 'secret')->withGroup(185)->create());

        $response->assertRedirect('/');
        $settings = app(NamiSettings::class);
        $this->assertEquals(12345, $settings->mglnr);
        $this->assertEquals('secret', $settings->password);
        $this->assertEquals(185, $settings->default_group_id);
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
        Auth::fails(12345, 'secret');

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
