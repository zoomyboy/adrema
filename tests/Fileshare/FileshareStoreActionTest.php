<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\NextcloudConnection;
use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\Fileshare;
use Tests\FileshareTestCase;

class FileshareStoreActionTest extends FileshareTestCase
{
    public function testItStoresAConnection(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withUser('badenpowell', 'secret');

        $this->post(route('fileshare.store'), [
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

    public function testItStoresNextcloudConnection(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withUser('badenpowell', 'uaLeitu3eecoweePhaeGei3Oa');

        $this->post(route('fileshare.store'), [
            'name' => 'Lala',
            'type' => NextcloudConnection::class,
            'config' => [
                'user' => 'badenpowell',
                'password' => 'uaLeitu3eecoweePhaeGei3Oa',
                'base_url' => env('TEST_NEXTCLOUD_DOMAIN'),
            ]
        ])->assertOk();

        $connection = Fileshare::firstOrFail();
        $this->assertEquals('badenpowell', $connection->type->user);
        $this->assertEquals('uaLeitu3eecoweePhaeGei3Oa', $connection->type->password);
        $this->assertEquals(env('TEST_NEXTCLOUD_DOMAIN'), $connection->type->baseUrl);
        $this->assertEquals('Lala', $connection->name);
    }
}
