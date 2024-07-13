<?php

namespace Tests;

use App\Fileshare\ConnectionTypes\ConnectionType;
use App\Fileshare\ConnectionTypes\NextcloudConnection;
use App\Fileshare\ConnectionTypes\OwncloudConnection;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;

abstract class FileshareTestCase extends TestCase
{
    use DatabaseTransactions;

    protected string $adminUser = 'admin';
    protected string $adminPassword = 'admin';

    /**
     * @var array<string, string>
     */
    protected array $passwords = [];

    public function setUp(): void
    {
        parent::setUp();

        foreach ($this->http(OwncloudConnection::class)->get('/ocs/v1.php/cloud/users?format=json')->json('ocs.data.users') as $user) {
            if ($user === $this->adminUser) {
                continue;
            }
            $this->http(OwncloudConnection::class)->delete('/ocs/v1.php/cloud/users/' . $user);
        }

        foreach ($this->http(NextcloudConnection::class)->get('/ocs/v2.php/cloud/users')->json('ocs.data.users') as $user) {
            if ($user === $this->adminUser) {
                continue;
            }
            $this->http(NextcloudConnection::class)->delete('/ocs/v2.php/cloud/users/' . $user);
        }
    }

    public function withUser(string $username, string $password): self
    {
        $this->passwords[$username] = $password;
        $this->http(OwncloudConnection::class)->asForm()->post('/ocs/v1.php/cloud/users?format=json', ['password' => $password, 'userid' => $username]);
        $this->http(NextcloudConnection::class)->post('/ocs/v2.php/cloud/users', ['password' => $password, 'userid' => $username]);

        return $this;
    }

    /**
     * @param class-string<ConnectionType> $connection
     */
    private function http(string $connection): PendingRequest
    {
        if ($connection === OwncloudConnection::class) {
            return Http::withOptions(['base_uri' => env('TEST_OWNCLOUD_DOMAIN')])->withBasicAuth($this->adminUser, $this->adminPassword)->acceptJson();
        }

        return Http::withOptions(['base_uri' => env('TEST_NEXTCLOUD_DOMAIN')])->withHeaders(['OCS-APIRequest' => 'true'])->withBasicAuth($this->adminUser, $this->adminPassword)->acceptJson();
    }

    /**
     * @param array<int, string> $dirs
     */
    protected function withDirs(string $username, array $dirs): self
    {
        foreach ([NextcloudConnection::class, OwncloudConnection::class] as $connection) {
            $adapter = $this->adapter($username, $connection);

            foreach ($adapter->directories('/') as $directory) {
                $adapter->deleteDirectory($directory);
            }

            foreach ($adapter->files('/') as $file) {
                $adapter->delete($file);
            }

            foreach ($dirs as $dir) {
                $adapter->makeDirectory($dir);
            }
        }

        return $this;
    }

    /**
     * @param class-string<ConnectionType> $connection
     */
    private function adapter(string $username, string $connection): FilesystemAdapter
    {
        if ($connection === OwncloudConnection::class) {
            $adapter = new WebDAVAdapter(new Client([
                'baseUri' => env('TEST_OWNCLOUD_DOMAIN') . '/remote.php/dav/files/' . $username,
                'userName' => $username,
                'password' => $this->passwords[$username],
            ]), '/remote.php/dav/files/' . $username);
        } else {
            $adapter = new WebDAVAdapter(new Client([
                'baseUri' => env('TEST_NEXTCLOUD_DOMAIN') . '/remote.php/dav/files/' . $username,
                'userName' => $username,
                'password' => $this->passwords[$username],
            ]), '/remote.php/dav/files/' . $username);
        }

        return new FilesystemAdapter(new Filesystem($adapter), $adapter);
    }
}
