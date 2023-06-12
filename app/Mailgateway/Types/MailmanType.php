<?php

namespace App\Mailgateway\Types;

use App\Maildispatcher\Data\MailEntry;
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
        return app(MailmanService::class)->setCredentials($this->url, $this->user, $this->password)->check();
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
        return null;
    }

    public function add(string $name, string $domain, string $email): void
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function list(string $name, string $domain): Collection
    {
        return collect([]);
    }

    public function remove(string $name, string $domain, string $email): void
    {
    }
}
