<?php

namespace App\Mailman\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class MailmanService
{
    private string $baseUrl;
    private string $username;
    private string $password;

    public function setCredentials(string $baseUrl, string $username, string $password): self
    {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    public function check(): bool
    {
        $response = $this->http()->get('/system/versions');

        return 200 === $response->status();
    }

    private function http(): PendingRequest
    {
        return Http::withBasicAuth($this->username, $this->password)->withOptions(['base_uri' => $this->baseUrl]);
    }
}
