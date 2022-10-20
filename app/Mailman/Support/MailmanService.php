<?php

namespace App\Mailman\Support;

use App\Mailman\Data\MailingList;
use App\Mailman\Exceptions\MailmanServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
use Zoomyboy\LaravelNami\Support\Paginator;

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
        try {
            $response = $this->http()->get('/system/versions');

            return 200 === $response->status();
        } catch (ConnectionException $e) {
            return false;
        }
    }

    /**
     * @return LazyCollection<int, string>
     */
    public function members(string $listId): LazyCollection
    {
        return app(Paginator::class)->result(
            fn ($page) => $this->http()->get("/lists/{$listId}/roster/member?page={$page}&count=10"),
            function ($response) use ($listId) {
                throw_unless($response->ok(), MailmanServiceException::class, 'Fetching members for listId '.$listId.' failed.');
                /** @var array<int, array{email: string}>|null */
                $entries = data_get($response->json(), 'entries');
                throw_if(is_null($entries), MailmanServiceException::class, 'Failed getting member list from response');

                foreach ($entries as $entry) {
                    yield $entry['email'];
                }
            },
            fn ($response) => data_get($response->json(), 'total_size')
        );
    }

    private function http(): PendingRequest
    {
        return Http::withBasicAuth($this->username, $this->password)->withOptions(['base_uri' => $this->baseUrl]);
    }

    public function getLists(): LazyCollection
    {
        return app(Paginator::class)->result(
            fn ($page) => $this->http()->get("/lists?page={$page}&count=10"),
            function ($response) {
                throw_unless($response->ok(), MailmanServiceException::class, 'Fetching lists failed.');
                /** @var array<int, array{email: string}>|null */
                $entries = data_get($response->json(), 'entries');
                throw_if(is_null($entries), MailmanServiceException::class, 'Failed getting lists from response');

                foreach ($entries as $entry) {
                    yield MailingList::from($entry);
                }
            },
            fn ($response) => data_get($response->json(), 'total_size')
        );
    }
}
