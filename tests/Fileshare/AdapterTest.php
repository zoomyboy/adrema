<?php

namespace Tests\Fileshare;

use App\Fileshare\ConnectionTypes\OwncloudConnection;
use App\Fileshare\Models\Fileshare;
use Tests\FileshareTestCase;

class AdapterTest extends FileshareTestCase
{
    public function testItGetsFilesInRoot(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withUser('badenpowell', 'oowoh2vahghai7aezahCeexae')
            ->withDirs('badenpowell', []);

        $storage = OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'oowoh2vahghai7aezahCeexae', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')])->getFilesystem();
        $storage->put('/test.pdf', '');
        $this->assertEquals(['test.pdf'], $storage->files('/'));
    }

    public function testItGetsFilesInSubdirectory(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami()->withUser('badenpowell', 'oowoh2vahghai7aezahCeexae')
            ->withDirs('badenpowell', ['/pictures']);

        $storage = OwncloudConnection::from(['user' => 'badenpowell', 'password' => 'oowoh2vahghai7aezahCeexae', 'base_url' => env('TEST_OWNCLOUD_DOMAIN')])->getFilesystem();
        $storage->put('/pictures/test.pdf', '');
        $this->assertEquals([], $storage->files('/'));
        $this->assertEquals(['pictures/test.pdf'], $storage->files('/pictures'));
    }
}
