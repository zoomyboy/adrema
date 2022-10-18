<?php

namespace Tests\Feature\Initialize;

use App\Console\Commands\NamiInitializeCommand;
use App\Initialize\Actions\InitializeAction;
use App\Initialize\InitializeJob;
use App\Initialize\Initializer;
use App\Setting\NamiSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Phake;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\Auth;
use Zoomyboy\LaravelNami\Fakes\GroupFake;

class RequestTest extends TestCase
{
    use DatabaseTransactions;

    public function testItSetsSettingsBeforeRunningInitializer(): void
    {
        $this->withoutExceptionHandling()->login();
        InitializeAction::partialMock()->shouldReceive('handle')->with(12345, 'secret', 185)->once()->andReturn(true);
        Auth::success(12345, 'secret');
        app(GroupFake::class)->fetches(null, [185 => ['name' => 'testgroup']]);

        $response = $this->post('/initialize', [
            'group_id' => 185,
            'password' => 'secret',
            'mglnr' => 12345,
        ]);

        $response->assertRedirect('/');
        $settings = app(NamiSettings::class);
        $this->assertEquals(12345, $settings->mglnr);
        $this->assertEquals('secret', $settings->password);
        $this->assertEquals(185, $settings->default_group_id);
    }

    public function testItValidatesSetupInfo(): void
    {
        $this->login();
        InitializeAction::partialMock()->shouldReceive('handle')->never();

        $response = $this->post('/initialize', [
            'group_id' => null,
            'password' => null,
            'mglnr' => null,
        ]);

        $this->assertErrors(['password' => 'Passwort ist erforderlich.'], $response);
        $this->assertErrors(['mglnr' => 'Mitgliedsnummer ist erforderlich.'], $response);
        $this->assertErrors(['group_id' => 'Gruppierungsnr ist erforderlich.'], $response);
    }

    public function testItValidatesLogin(): void
    {
        $this->login();
        Auth::fails(12345, 'secret');
        InitializeAction::partialMock()->shouldReceive('handle')->never();

        $response = $this->post('/initialize', [
            'group_id' => 12345,
            'password' => 'secret',
            'mglnr' => 100102,
        ]);

        $this->assertErrors(['nami' => 'NaMi Login fehlgeschlagen.'], $response);
    }

    public function testItValidatesGroupExistance(): void
    {
        $this->login();
        InitializeAction::partialMock()->shouldReceive('handle')->never();
        Auth::success(12345, 'secret');
        app(GroupFake::class)->fetches(null, []);

        $response = $this->post('/initialize', [
            'group_id' => 185,
            'password' => 'secret',
            'mglnr' => 12345,
        ]);

        $this->assertErrors(['nami' => 'Gruppierung nicht gefunden.'], $response);
    }

    public function testItFiresJobWhenRunningInitializer(): void
    {
        Queue::fake();
        $this->withoutExceptionHandling()->login();

        app(InitializeAction::class)->handle(12345, 'secret', 185);

        Queue::assertPushed(InitializeJob::class);
    }

    public function testItInitializesFromCommandLine(): void
    {
        $this->stubIo(Initializer::class, fn ($mock) => $mock);

        Artisan::call(NamiInitializeCommand::class);

        Phake::verify(app(Initializer::class))->run();
    }
}
