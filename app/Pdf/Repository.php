<?php

namespace App\Pdf;

abstract class Repository
{

    public function number(int $number): string
    {
        return number_format($number / 100, 2, '.', '');
    }

}
