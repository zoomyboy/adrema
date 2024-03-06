<?php

namespace App\Form\Data;

use Spatie\LaravelData\Data;

class ColumnData extends Data
{

    public function __construct(
        public int $mobile,
        public int $tablet,
        public int $desktop,
    ) {
    }
}
