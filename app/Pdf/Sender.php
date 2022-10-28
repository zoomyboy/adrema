<?php

namespace App\Pdf;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class Sender extends Data
{
    public function __construct(public string $name, public string $address, public string $zipLocation, public Lazy|string $mglnr)
    {
    }

    /**
     * @return array<int, string>
     */
    public function values(): array
    {
        return array_values($this->include('mglnr')->toArray());
    }
}
