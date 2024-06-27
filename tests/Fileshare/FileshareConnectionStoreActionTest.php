<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\FileshareConnection;
use Tests\FileshareTestCase;

class FileshareConnectionStoreActionTest extends FileshareTestCase
{
    public function testItStoresAConnection(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withOwncloudUser('badenpowell', 'secret');

        $this->post(route('fileshare.store'), [
            'name' => 'Lala',
            'type' => OwncloudConnection::class,
            'config' => [
                'user' => 'badenpowell',
                'password' => 'secret',
                'base_url' => env('TEST_OWNCLOUD_DOMAIN'),
            ]
        ])->assertOk();

        $connection = FileshareConnection::firstOrFail();
        $this->assertEquals('badenpowell', $connection->type->user);
        $this->assertEquals('secret', $connection->type->password);
        $this->assertEquals(env('TEST_OWNCLOUD_DOMAIN'), $connection->type->baseUrl);
        $this->assertEquals('Lala', $connection->name);
    }

    public function testItChecksConnection(): void
    {
        $this->withExceptionHandling()->login()->loginNami();

        $this->postJson(route('fileshare.store'), [
            'name' => 'Lala',
            'type' => OwncloudConnection::class,
            'config' => [
                'user' => 'badenpowell',
                'password' => 'secret',
                'base_url' => env('TEST_OWNCLOUD_DOMAIN'),
            ]
        ])->assertJsonValidationErrors(['type' => 'Verbindung fehlgeschlagen']);
    }

    public function testItNeedsName(): void
    {
        $this->withExceptionHandling()->login()->loginNami();

        $this->postJson(route('fileshare.store'), [
            'name' => '',
            'type' => OwncloudConnection::class,
            'config' => [
                'user' => 'badenpowell',
                'password' => 'secret',
                'base_url' => env('TEST_OWNCLOUD_DOMAIN'),
            ]
        ])->assertJsonValidationErrors(['name' => 'Name ist erforderlich.']);
    }
}
