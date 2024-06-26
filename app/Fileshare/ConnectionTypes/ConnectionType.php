<?php

namespace App\Fileshare\ConnectionTypes;

use Spatie\LaravelData\Data;

abstract class ConnectionType extends Data
{
    abstract public function check(): bool;
}
