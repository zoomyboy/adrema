<?php

namespace App\Mailman\Support;

use App\Mailman\Data\MailingList;
use App\Mailman\Data\Member;
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
    private string $owner;

    public function setCredentials(string $baseUrl, string $username, string $password): self
    {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function createList(string $mailAddress): MailingList
    {
        $response = $this->http()->post('/lists', [
            'fqdn_listname' => $mailAddress,
        ]);
        throw_unless(201 === $response->status(), MailmanServiceException::class, 'Creating list failed');
        $list = $this->getLists()->first(fn ($list) => $list->fqdnListname === $mailAddress);
        $response = $this->http()->patch("/lists/{$list->listId}/config", [
            'advertised' => 'False',
            'respond_to_post_requests' => 'False',
            'send_goodbye_message' => 'False',
            'send_welcome_message' => 'False',
            'admin_immed_notify' => 'True',
        ]);
        throw_unless(204 === $response->status(), MailmanServiceException::class, 'Updating list config failed');
        $response = $this->http()->post('/members', [
            'list_id' => $list->listId,
            'subscriber' => $this->owner,
            'role' => 'owner',
        ]);
        throw_unless(201 === $response->status(), MailmanServiceException::class, 'Creating list owner failed');

        return $list;
    }

    public function deleteList(string $mailAddress): void
    {
        $list = $this->getLists()->first(fn ($list) => $list->fqdnListname === $mailAddress);
        $response = $this->http()->delete("/lists/{$list->listId}");
        throw_unless(204 === $response->status(), MailmanServiceException::class, 'Deleting list failed');
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
     * @return LazyCollection<int, Member>
     */
    public function members(MailingList $list): LazyCollection
    {
        return app(Paginator::class)->result(
            fn ($page) => $this->http()->get("/lists/{$list->listId}/roster/member?page={$page}&count=10"),
            function ($response) use ($list) {
                throw_unless($response->ok(), MailmanServiceException::class, 'Fetching members for listId '.$list->listId.' failed.');
                /** @var array<int, array{email: string, self_link: string}>|null */
                $entries = data_get($response->json(), 'entries', []);
                throw_if(is_null($entries), MailmanServiceException::class, 'Failed getting member list from response');

                foreach ($entries as $entry) {
                    yield Member::from([
                        ...$entry,
                        'member_id' => strrev(preg_split('/\//', strrev($entry['self_link']))[0]),
                    ]);
                }
            },
            fn ($response) => data_get($response->json(), 'total_size')
        );
    }

    public function addMember(MailingList $list, string $email): void
    {
        $response = $this->http()->post('members', [
            'list_id' => $list->listId,
            'subscriber' => $email,
            'pre_verified' => 'true',
            'pre_approved' => 'true',
            'send_welcome_message' => 'false',
            'pre_confirmed' => 'true',
        ]);

        throw_unless(201 === $response->status(), MailmanServiceException::class, 'Adding member '.$email.' to '.$list->listId.' failed');
    }

    public function removeMember(Member $member): void
    {
        $response = $this->http()->delete("members/{$member->memberId}");

        throw_unless(204 === $response->status(), MailmanServiceException::class, 'Removing member failed');
    }

    private function http(): PendingRequest
    {
        return Http::withBasicAuth($this->username, $this->password)->withOptions(['base_uri' => $this->baseUrl]);
    }

    /**
     * @return LazyCollection<int, MailingList>
     */
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
