<?php

namespace App\Lib\Editor;

use Spatie\LaravelData\Data;

class Statement extends Data
{
    /** @param mixed $value */
    public function __construct(public string $field, public $value, public Comparator $comparator)
    {
    }
}
