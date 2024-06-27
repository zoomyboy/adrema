<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\Fileshare;
use Tests\FileshareTestCase;

class FileshareUpdateActionTest extends FileshareTestCase
{
    public function testItStoresAConnection(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withOwncloudUser('badenpowell', 'secret');

        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'test', 'password' => 'test', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->name('lokaler Server')
            ->create();

        $this->patch(route('fileshare.update', ['fileshare' => $connection]), [
            'name' => 'Lala',
            'type' => OwncloudConnection::class,
            'config' => [
                'user' => 'badenpowell',
                'password' => 'secret',
                'base_url' => env('TEST_OWNCLOUD_DOMAIN'),
            ]
        ])->assertOk();

        $connection = Fileshare::firstOrFail();
        $this->assertEquals('badenpowell', $connection->type->user);
        $this->assertEquals('secret', $connection->type->password);
        $this->assertEquals(env('TEST_OWNCLOUD_DOMAIN'), $connection->type->baseUrl);
        $this->assertEquals('Lala', $connection->name);
    }

    public function testItChecksConnection(): void
    {
        $this->login()->loginNami()->withOwncloudUser('test', 'test');

        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'test', 'password' => 'test', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->name('lokaler Server')
            ->create();

        $this->patchJson(route('fileshare.update', ['fileshare' => $connection]), [
            'name' => 'Lala',
            'type' => OwncloudConnection::class,
            'config' => [
                'user' => 'badenpowell',
                'password' => 'secret',
                'base_url' => env('TEST_OWNCLOUD_DOMAIN'),
            ]
        ])->assertJsonValidationErrors(['type' => 'Verbindung fehlgeschlagen']);
    }
}
