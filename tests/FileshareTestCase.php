<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

abstract class FileshareTestCase extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser = 'admin';
    protected $adminPassword = 'admin';

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
        $this->http()->asForm()->post('/ocs/v1.php/cloud/users?format=json', ['password' => $password, 'userid' => $username]);

        return $this;
    }

    private function http(): PendingRequest
    {
        return Http::withOptions(['base_uri' => env('TEST_OWNCLOUD_DOMAIN')])->withBasicAuth($this->adminUser, $this->adminPassword)->acceptJson();
    }
}
