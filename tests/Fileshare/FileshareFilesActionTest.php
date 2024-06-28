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

        $this->postJson(route('fileshare.files', ['fileshare' => $connection]), [
            'parent' => null,
        ])->assertJsonPath('data.0.name', 'pictures')
            ->assertJsonPath('data.1.name', 'lala');
    }
}
