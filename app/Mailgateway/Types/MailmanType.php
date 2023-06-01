<?php

namespace App\Mailgateway\Types;

class MailmanType extends Type
{
    public static function name(): string
    {
        return 'Mailman';
    }

    public function works(): bool
    {
        return true;
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
