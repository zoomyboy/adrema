<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\Fileshare;
use Tests\FileshareTestCase;

class FileshareFilesActionTest extends FileshareTestCase
{
    public function testItGetsFilesForAConnection(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withOwncloudUser('badenpowell', 'secret')
            ->withDirs('badenpowell', ['/pictures', '/lala']);

        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->create();

        $this->postJson(route('api.fileshare.files', ['fileshare' => $connection]), [
            'parent' => null,
        ])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'lala')
            ->assertJsonPath('data.0.path', '/lala')
            ->assertJsonPath('data.0.parent', '/')
            ->assertJsonPath('data.1.name', 'pictures')
            ->assertJsonPath('data.1.path', '/pictures')
            ->assertJsonPath('data.1.parent', '/');
    }

    public function testItGetsSubdirectories(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withOwncloudUser('badenpowell', 'secret')
            ->withDirs('badenpowell', ['/pictures', '/lala', '/lala/dd', '/lala/ff']);

        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->create();

        $this->postJson(route('api.fileshare.files', ['fileshare' => $connection]), ['parent' => '/pictures'])->assertJsonCount(0, 'data');
        $this->postJson(route('api.fileshare.files', ['fileshare' => $connection]), ['parent' => '/lala'])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'dd')
            ->assertJsonPath('data.0.path', '/lala/dd')
            ->assertJsonPath('data.0.parent', '/lala')
            ->assertJsonPath('data.1.name', 'ff')
            ->assertJsonPath('data.1.path', '/lala/ff')
            ->assertJsonPath('data.1.parent', '/lala');
    }

    public function testItGetsSubdirectoriesOfSubdirectory(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withOwncloudUser('badenpowell', 'secret')
            ->withDirs('badenpowell', ['/lala', '/lala/dd', '/lala/dd/ee']);

        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->create();

        $this->postJson(route('api.fileshare.files', ['fileshare' => $connection]), ['parent' => '/lala/dd'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'ee')
            ->assertJsonPath('data.0.path', '/lala/dd/ee')
            ->assertJsonPath('data.0.parent', '/lala/dd');
    }

    public function testItGetsFilesWithDot(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withOwncloudUser('badenpowell', 'secret')
            ->withDirs('badenpowell', ['/1. aa']);

        $connection = Fileshare::factory()
            ->type(OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'secret', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')]))
            ->create();

        $this->postJson(route('api.fileshare.files', ['fileshare' => $connection]), ['parent' => '/'])
            ->assertJsonPath('data.0.name', '1. aa');
    }
}
