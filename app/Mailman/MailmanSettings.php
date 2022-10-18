<?php

namespace App\Mailman;

use Spatie\LaravelSettings\Settings;

class MailmanSettings extends Settings
{
    public ?string $base_url;

    public ?string $username;

    public ?string $password;

    public static function group(): string
    {
        return 'mailman';
    }
}
