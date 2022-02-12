<?php

namespace Tests\Feature;

use App\Setting\GeneralSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Authentication\NamiGuard;
use Zoomyboy\LaravelNami\Backend\FakeBackend;

class NamiLoginTest extends TestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testItCanLoginWithANamiAccount(): void
    {
        $this->withoutExceptionHandling();
        $this->setLoginId(123);
        app(FakeBackend::class)
            ->fakeLogin('123')
            ->addSearch(123, ['entries_vorname' => '::firstname::', 'entries_nachname' => '::lastname::', 'entries_gruppierungId' => 1000]);

        $this->post('/login', [
            'mglnr' => 123,
            'provider' => 'nami',
            'password' => 'secret'
        ]);

        $key = session()->get('auth_key');
        $cache = Cache::get("namiauth-{$key}");
        $this->assertEquals('secret', data_get($cache, 'credentials.password'));
        $this->assertEquals('::firstname::', data_get($cache, 'firstname'));
        $this->assertEquals('::lastname::', data_get($cache, 'lastname'));
        $this->assertEquals(1000, data_get($cache, 'group_id'));
        $this->assertEquals(123, data_get($cache, 'credentials.mglnr'));
        $this->assertTrue(auth()->check());
    }

    public function testItDoesntLoginTwoTimes(): void
    {
        $this->withoutExceptionHandling();
        $this->setLoginId(123);
        app(FakeBackend::class)
            ->fakeLogin('123')
            ->addSearch(123, ['entries_vorname' => '::firstname::', 'entries_nachname' => '::lastname::', 'entries_gruppierungId' => 1000]);

        $this->post('/login', [
            'mglnr' => 123,
            'provider' => 'nami',
            'password' => 'secret'
        ]);
        auth()->logout();
        $this->post('/login', [
            'mglnr' => 123,
            'provider' => 'nami',
            'password' => 'secret'
        ]);

        $this->assertTrue(auth()->check());

        Http::assertSentCount(4);
    }

    public function testItResolvesTheLoginFromTheCache(): void
    {
        $this->withoutExceptionHandling();
        $this->setLoginId(123);
        app(FakeBackend::class)
            ->fakeLogin('123')
            ->addSearch(123, ['entries_vorname' => '::firstname::', 'entries_nachname' => '::lastname::', 'entries_gruppierungId' => 1000]);

        $this->post('/login', [
            'mglnr' => 123,
            'provider' => 'nami',
            'password' => 'secret'
        ]);
        app(NamiGuard::class)->setUser(null);
        $this->post('/login', [
            'mglnr' => 123,
            'provider' => 'nami',
            'password' => 'secret'
        ]);

        $this->assertTrue(auth()->check());

        Http::assertSentCount(3);
    }

    public function testItThrowsExceptionWhenLoginFailed(): void
    {
        $this->setLoginId(123);
        app(FakeBackend::class)->fakeFailedLogin();

        $this->post('/login', [
            'mglnr' => 123,
            'provider' => 'nami',
            'password' => 'secret'
        ])->assertRedirect('/');

        $this->assertFalse(auth()->check());

        Http::assertSentCount(2);
    }

    public function testItCannotLoginWithAWrongNamiId(): void
    {
        app(FakeBackend::class)
            ->fakeLogin('123')
            ->addSearch(123, ['entries_vorname' => '::firstname::', 'entries_nachname' => '::lastname::', 'entries_gruppierungId' => 1000]);

        $this->post('/login', [
            'mglnr' => 123,
            'provider' => 'nami',
            'password' => 'secret'
        ])->assertRedirect('/');

        $this->assertTrue(auth()->guest());

        Http::assertSentCount(0);
    }

    private function setLoginId(int $mglNr): self
    {
        GeneralSettings::fake([
            'allowed_nami_accounts' => [$mglNr]
        ]);

        return $this;
    }

}
