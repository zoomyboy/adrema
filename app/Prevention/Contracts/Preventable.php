<?php

namespace App\Prevention\Contracts;

use App\Prevention\Enums\Prevention;
use stdClass;

interface Preventable
{

    public function preventableLayout(): string;
    public function preventableSubject(): string;

    /**
     * @return array<int, Prevention>
     */
    public function preventions(): array;

    public function getMailRecipient(): stdClass;
}
