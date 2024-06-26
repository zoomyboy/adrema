<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\FileshareConnection;
use Tests\FileshareTestCase;

class ConnectionIndexActionTest extends FileshareTestCase
{
    public function testItListsOwncloudConnectionsThatAreActive(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withOwncloudUser('badenpowell', 'secret');
        $connection = FileshareConnection::factory()
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
            ->assertInertiaPath('data.data.0.is_active', true);
    }
}
