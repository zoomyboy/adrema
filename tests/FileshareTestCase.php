<?php

namespace Tests;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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

        foreach ($this->http()->get('/ocs/v1.php/cloud/users?format=json')->json('ocs.data.users') as $user) {
            if ($user === $this->adminUser) {
                continue;
            }
            $this->http()->delete('/ocs/v1.php/cloud/users/' . $user);
        }
    }

    public function withOwncloudUser(string $username, string $password): self
    {
        $this->passwords[$username] = $password;
        $this->http()->asForm()->post('/ocs/v1.php/cloud/users?format=json', ['password' => $password, 'userid' => $username]);

        return $this;
    }

    private function http(): PendingRequest
    {
        return Http::withOptions(['base_uri' => env('TEST_OWNCLOUD_DOMAIN')])->withBasicAuth($this->adminUser, $this->adminPassword)->acceptJson();
    }

    /**
     * @param array<int, string> $dirs
     */
    protected function withDirs(string $username, array $dirs): self
    {
        $adapter = $this->adapter($username);

        foreach ($adapter->directories() as $directory) {
            $adapter->deleteDirectory($directory);
        }
        dd($adapter->directories());

        $adapter->makeDirectory('lala');
        $adapter->makeDirectory('aa/zz');
        $adapter->makeDirectory('aa/zz');
        dd($adapter->directories());
        // dd(array_map(fn ($d) => str_replace('remote.php/dav/files/' . $username, '', $d), $adapter->directories()));
    }

    private function adapter(string $username): FilesystemAdapter
    {
        $adapter = new WebDAVAdapter(new Client([
            'baseUri' => env('TEST_OWNCLOUD_DOMAIN') . '/remote.php/dav/files/' . $username,
            'userName' => $username,
            'password' => $this->passwords[$username],
        ]), '/remote.php/dav/files/' . $username);

        return new FilesystemAdapter(new Filesystem($adapter), $adapter);
    }
}
