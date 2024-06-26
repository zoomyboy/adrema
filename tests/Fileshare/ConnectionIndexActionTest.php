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
        FileshareConnection::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->name('lokaler Server')
            ->create();

        $this->get('/setting/fileshare')
            ->assertInertiaPath('data.data.0.name', 'lokaler Server')
            ->assertInertiaPath('data.data.0.is_active', true);
    }
}
