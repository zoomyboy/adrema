<?php

namespace Tests\Feature\Mailman;

use App\Mailman\Data\MailingList;
use App\Mailman\MailmanSettings;
use App\Mailman\Support\MailmanService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\LazyCollection;
use Phake;
use Tests\RequestFactories\MailmanListRequestFactory;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseTransactions;

    public function testItGetsMailSettings(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->stubIo(MailmanService::class, function ($mock) {
            Phake::when($mock)->fromSettings(Phake::anyParameters())->thenReturn($mock);
            Phake::when($mock)->check()->thenReturn(true);
            Phake::when($mock)->getLists()->thenReturn(LazyCollection::make(function () {
                yield MailingList::from(MailmanListRequestFactory::new()->create(['list_id' => 'F', 'fqdn_listname' => 'admin@example.com']));
            }));
        });
        MailmanSettings::fake([
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => 'secret',
            'is_active' => true,
        ]);

        $response = $this->get('/setting/mailman');

        $response->assertOk();
        $this->assertInertiaHas([
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => '',
            'is_active' => true,
        ], $response, 'data');
        $this->assertInertiaHas(true, $response, 'state');
        $this->assertInertiaHas('admin@example.com', $response, 'lists.0.name');
        $this->assertInertiaHas('F', $response, 'lists.0.id');
    }

    public function testItReturnsWrongStateWhenLoginFailed(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->stubIo(MailmanService::class, function ($mock) {
            Phake::when($mock)->fromSettings(Phake::anyParameters())->thenReturn($mock);
            Phake::when($mock)->check()->thenReturn(false);
        });
        MailmanSettings::fake([
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => 'secret',
            'is_active' => true,
        ]);

        $response = $this->get('/setting/mailman');

        $response->assertOk();
        $this->assertInertiaHas(false, $response, 'state');
    }

    public function testItDoesntReturnAnyStateWhenMailmanIsInactive(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->stubIo(MailmanService::class, fn ($mock) => $mock);
        MailmanSettings::fake([
            'base_url' => 'http://mailman.test/api',
            'username' => 'user',
            'password' => 'secret',
            'is_active' => false,
        ]);

        $response = $this->get('/setting/mailman');

        $response->assertOk();
        $this->assertInertiaHas(null, $response, 'state');
        Phake::verifyNoInteraction(app(MailmanService::class));
    }

    public function testItSetsMailmanSettings(): void
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
            'is_active' => true,
            'all_parents_list' => 'P',
            'all_list' => 'X',
        ]);

        $response->assertRedirect('/setting/mailman');
        $settings = app(MailmanSettings::class);
        $this->assertEquals('http://mailman.test/api', $settings->base_url);
        $this->assertEquals('secret', $settings->password);
        $this->assertEquals('user', $settings->username);
        $this->assertEquals('X', $settings->all_list);
        $this->assertEquals('P', $settings->all_parents_list);
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
            'is_active' => true,
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
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors(['password' => 'Passwort ist erforderlich.']);
        Phake::verifyNoInteraction(app(MailmanService::class));
    }
}
