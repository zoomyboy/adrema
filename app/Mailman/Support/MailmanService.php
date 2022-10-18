<?php

namespace App\Mailman\Support;

use App\Mailman\Exceptions\MailmanServiceException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;

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

    /**
     * @return LazyCollection<int, string>
     */
    public function members(string $listId): LazyCollection
    {
        $page = 1;

        return LazyCollection::make(function () use ($listId, $page) {
            while (!isset($totalEntries) || ($page - 1) * 10 + 1 <= $totalEntries) {
                $response = $this->http()->get('/lists/'.$listId.'/roster/member?page='.$page.'&count=10');
                throw_unless($response->ok(), MailmanServiceException::class, 'Fetching members for listId '.$listId.' failed.');
                /** @var array<int, array{email: string}>|null */
                $entries = data_get($response->json(), 'entries');
                throw_if(is_null($entries), MailmanServiceException::class, 'Failed getting member list from response');
                $totalEntries = data_get($response->json(), 'total_size');

                foreach ($entries as $entry) {
                    yield $entry['email'];
                }

                ++$page;
            }
        });
    }

    private function http(): PendingRequest
    {
        return Http::withBasicAuth($this->username, $this->password)->withOptions(['base_uri' => $this->baseUrl]);
    }
}
