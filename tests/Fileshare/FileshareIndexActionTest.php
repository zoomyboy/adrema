<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\NextcloudConnection;
use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\Fileshare;
use Tests\FileshareTestCase;

class FileshareIndexActionTest extends FileshareTestCase
{
    public function testItListsOwncloudConnectionsThatAreActive(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withUser('badenpowell', 'secret');
        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->name('lokaler Server')
            ->create();

        $this->get('/api/fileshare')
            ->assertJsonPath('data.0.name', 'lokaler Server')
            ->assertJsonPath('data.0.type', OwncloudConnection::class)
            ->assertJsonPath('data.0.config.user', 'badenpowell')
            ->assertJsonPath('data.0.config.password', 'secret')
            ->assertJsonPath('data.0.config.base_url', env('TEST_OWNCLOUD_DOMAIN'))
            ->assertJsonPath('data.0.id', $connection->id)
            ->assertJsonPath('data.0.is_active', true)
            ->assertJsonPath('data.0.type_human', 'Owncloud')
            ->assertJsonPath('data.0.links.update', route('fileshare.update', ['fileshare' => $connection]))
            ->assertJsonPath('meta.default.name', '')
            ->assertJsonPath('meta.links.store', route('fileshare.store'))
            ->assertJsonPath('meta.types.0.id', NextcloudConnection::class)
            ->assertJsonPath('meta.types.0.name', 'Nextcloud')
            ->assertJsonPath('meta.types.0.defaults.base_url', '')
            ->assertJsonPath('meta.types.1.id', OwncloudConnection::class)
            ->assertJsonPath('meta.types.1.name', 'Owncloud')
            ->assertJsonPath('meta.types.1.defaults.base_url', '')
            ->assertJsonPath('meta.types.0.fields.1', ['label' => 'Benutzer', 'key' => 'user', 'type' => 'text']);
    }

    public function testItRendersComponent(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();

        $this->get('/setting/fileshare')->assertComponent('fileshare/Index');
    }
}
