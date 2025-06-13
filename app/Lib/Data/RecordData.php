<?php

namespace App\Lib\Data;

use Spatie\LaravelData\Data;

class RecordData extends Data {

    public function __construct(
        public int $id,
        public string $name,
    ) {}

}
