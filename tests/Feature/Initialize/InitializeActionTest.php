<?php

namespace Tests\Feature\Initialize;

use App\Initialize\Actions\InitializeAction;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\RequestFactories\InitializeRequestFactory;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;

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

    public function testItSetsSettings(): void
    {
        Queue::fake();
        Auth::success(100222, 'secret');
        $this->login();

        $response = $this->post('/initialize', $this->factory()->withCredentials(100222, 'secret')->withGroup(77)->withParams(['gruppierung1Id' => '66777'])->create());

        $response->assertRedirect('/');
        $settings = app(NamiSettings::class);
        $this->assertEquals(100222, $settings->mglnr);
        $this->assertEquals('secret', $settings->password);
        $this->assertEquals(77, $settings->default_group_id);
        $this->assertEquals('66777', $settings->search_params['gruppierung1Id']);
    }

    private function factory(): InitializeRequestFactory
    {
        return InitializeRequestFactory::new();
    }
}
