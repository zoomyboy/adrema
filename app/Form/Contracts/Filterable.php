<?php

namespace App\Form\Contracts;

interface Filterable
{
    /** @param mixed $value */
    public function filter($value): string;
}
