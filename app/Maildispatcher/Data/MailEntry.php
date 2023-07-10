<?php

namespace App\Maildispatcher\Data;

use Spatie\LaravelData\Data;

class MailEntry extends Data
{
    public function __construct(public string $email)
    {
        $this->email = strtolower($email);
    }

    public function is(self $mailEntry): bool
    {
        return $this->email === $mailEntry->email;
    }
}
