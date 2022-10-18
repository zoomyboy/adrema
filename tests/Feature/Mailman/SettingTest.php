<?php

namespace Tests\Feature\Mailman;

use App\Mailman\MailmanSettings;
use App\Mailman\Support\MailmanService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phake;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseTransactions;

    public function testItGetsMailSettings(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        MailmanSettings::fake([
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => 'secret',
        ]);

        $response = $this->get('/setting/mailman');

        $response->assertOk();
        $this->assertInertiaHas([
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => '',
        ], $response, 'data');
    }

    public function testItSaesMailmanSettings(): void
    {
        $this->stubIo(MailmanService::class, function ($mock) {
            Phake::when($mock)->setCredentials('http://mailman.test/api', 'user', 'secret')->thenReturn($mock);
            Phake::when($mock)->check()->thenReturn(true);
        });
        $this->withoutExceptionHandling()->login()->loginNami();

        $response = $this->from('/setting/mailman')->post('/setting/mailman', [
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => 'secret',
        ]);

        $response->assertRedirect('/setting/mailman');
        $settings = app(MailmanSettings::class);
        $this->assertEquals('http://mailman.test/api', $settings->base_url);
        $this->assertEquals('secret', $settings->password);
        $this->assertEquals('user', $settings->username);
        Phake::verify(app(MailmanService::class))->setCredentials('http://mailman.test/api', 'user', 'secret');
        Phake::verify(app(MailmanService::class))->check();
    }

    public function testItThrowsErrorWhenLoginFailed(): void
    {
        $this->stubIo(MailmanService::class, function ($mock) {
            Phake::when($mock)->setCredentials('http://mailman.test/api', 'user', 'secret')->thenReturn($mock);
            Phake::when($mock)->check()->thenReturn(false);
        });
        $this->login()->loginNami();

        $response = $this->from('/setting/mailman')->post('/setting/mailman', [
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => 'secret',
        ]);

        $response->assertSessionHasErrors(['mailman' => 'Verbindung fehlgeschlagen.']);
        Phake::verify(app(MailmanService::class))->setCredentials('http://mailman.test/api', 'user', 'secret');
        Phake::verify(app(MailmanService::class))->check();
    }

    public function testItValidatesPassword(): void
    {
        $this->stubIo(MailmanService::class, fn ($mock) => $mock);
        $this->login()->loginNami();

        $response = $this->from('/setting/mailman')->post('/setting/mailman', [
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'Passwort ist erforderlich.']);
        Phake::verifyNoInteraction(app(MailmanService::class));
    }
}
