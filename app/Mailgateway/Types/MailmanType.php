<?php

namespace App\Mailgateway\Types;

use App\Maildispatcher\Data\MailEntry;
use App\Mailman\Exceptions\MailmanServiceException;
use App\Mailman\Support\MailmanService;
use Illuminate\Support\Collection;

class MailmanType extends Type
{
    public string $url;
    public string $user;
    public string $password;

    public function setParams(array $params): static
    {
        $this->url = data_get($params, 'url');
        $this->user = data_get($params, 'user');
        $this->password = data_get($params, 'password');

        return $this;
    }

    public static function name(): string
    {
        return 'Mailman';
    }

    public function works(): bool
    {
        return $this->service()->check();
    }

    /**
     * {@inheritdoc}
     */
    public static function fields(): array
    {
        return [
            [
                'name' => 'url',
                'label' => 'URL',
                'type' => 'text',
                'storeValidator' => 'required|max:255',
                'updateValidator' => 'required|max:255',
                'default' => '',
            ],
            [
                'name' => 'user',
                'label' => 'Benutzer',
                'type' => 'text',
                'storeValidator' => 'required|max:255',
                'updateValidator' => 'required|max:255',
                'default' => '',
            ],
            [
                'name' => 'password',
                'label' => 'Passwort',
                'type' => 'password',
                'storeValidator' => 'required|max:255',
                'updateValidator' => 'nullable|max:255',
                'default' => '',
            ],
        ];
    }

    public function search(string $name, string $domain, string $email): ?MailEntry
    {
        $list = $this->service()->getLists()->first(fn ($list) => $list->fqdnListname === "{$name}@{$domain}");
        throw_if(!$list, MailmanServiceException::class, "List for {$name}@{$domain} not found");
        $member = $this->service()->members($list)->first(fn ($member) => $member->email === $email);

        return $member ? MailEntry::from(['email' => $member->email]) : null;
    }

    public function add(string $name, string $domain, string $email): void
    {
        $list = $this->service()->getLists()->first(fn ($list) => $list->fqdnListname === "{$name}@{$domain}");
        throw_if(!$list, MailmanServiceException::class, "List for {$name}@{$domain} not found");
        $this->service()->addMember($list, $email);
    }

    /**
     * {@inheritdoc}
     */
    public function list(string $name, string $domain): Collection
    {
        $list = $this->service()->getLists()->first(fn ($list) => $list->fqdnListname === "{$name}@{$domain}");
        throw_if(!$list, MailmanServiceException::class, "List for {$name}@{$domain} not found");

        return collect($this->service()->members($list)->map(fn ($member) => MailEntry::from(['email' => $member->email]))->all());
    }

    public function remove(string $name, string $domain, string $email): void
    {
        $list = $this->service()->getLists()->first(fn ($list) => $list->fqdnListname === "{$name}@{$domain}");
        throw_if(!$list, MailmanServiceException::class, "List for {$name}@{$domain} not found");
        $member = $this->service()->members($list)->first(fn ($member) => $member->email === $email);
        throw_if(!$member, MailmanServiceException::class, 'Member for removing not found');
        $this->service()->removeMember($member);
    }

    private function service(): MailmanService
    {
        return app(MailmanService::class)->setCredentials($this->url, $this->user, $this->password);
    }
}
