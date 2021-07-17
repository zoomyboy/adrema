<?php

namespace App\Pdf;

use Carbon\Carbon;

abstract class Repository
{

    public function number(int $number): string
    {
        return number_format($number / 100, 2, '.', '');
    }

    public function getUntil(): Carbon
    {
        return now()->addWeeks(2);
    }

}
