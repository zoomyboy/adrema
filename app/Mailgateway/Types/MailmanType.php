<?php

namespace App\Mailgateway\Types;

use App\Mailman\Support\MailmanService;

class MailmanType extends Type
{
    public string $url;
    public string $user;
    public string $password;

    public function __construct($params)
    {
        $this->url = data_get($params, 'url');
        $this->user = data_get($params, 'user');
        $this->password = data_get($params, 'password');
    }

    public static function name(): string
    {
        return 'Mailman';
    }

    public function works(): bool
    {
        return app(MailmanService::class)->setCredentials($this->url, $this->user, $this->password)->check();
    }

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
                'type' => 'text',
                'storeValidator' => 'required|max:255',
                'updateValidator' => 'nullable|max:255',
                'default' => '',
            ],
        ];
    }
}
