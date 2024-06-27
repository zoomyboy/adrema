<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\Fileshare;
use Tests\FileshareTestCase;

class FileshareIndexActionTest extends FileshareTestCase
{
    public function testItListsOwncloudConnectionsThatAreActive(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withOwncloudUser('badenpowell', 'secret');
        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->name('lokaler Server')
            ->create();

        $this->get('/setting/fileshare')
            ->assertInertiaPath('data.data.0.name', 'lokaler Server')
            ->assertInertiaPath('data.data.0.type', OwncloudConnection::class)
            ->assertInertiaPath('data.data.0.config.user', 'badenpowell')
            ->assertInertiaPath('data.data.0.config.password', 'secret')
            ->assertInertiaPath('data.data.0.config.base_url', env('TEST_OWNCLOUD_DOMAIN'))
            ->assertInertiaPath('data.data.0.id', $connection->id)
            ->assertInertiaPath('data.data.0.is_active', true)
            ->assertInertiaPath('data.data.0.type_human', 'Owncloud')
            ->assertInertiaPath('data.data.0.links.update', route('fileshare.update', ['fileshare' => $connection]))
            ->assertInertiaPath('data.meta.default.name', '')
            ->assertInertiaPath('data.meta.links.store', route('fileshare.store'))
            ->assertInertiaPath('data.meta.types.0.id', OwncloudConnection::class)
            ->assertInertiaPath('data.meta.types.0.name', 'Owncloud')
            ->assertInertiaPath('data.meta.types.0.defaults.base_url', '')
            ->assertInertiaPath('data.meta.types.0.fields.1', ['label' => 'Benutzer', 'key' => 'user', 'type' => 'text']);
    }
}
