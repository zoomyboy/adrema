<?php

namespace App\Setting\Contracts;

interface Indexable
{
    /**
     * @return class-string
     */
    public static function indexAction(): string;
}
