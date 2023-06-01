<?php

namespace App\Mailgateway\Types;

abstract class Type
{
    abstract public static function name(): string;

    abstract public function works(): bool;
}
