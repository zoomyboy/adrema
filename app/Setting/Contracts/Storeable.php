<?php

namespace App\Setting\Contracts;

interface Storeable
{
    /**
     * @return class-string
     */
    public static function storeAction(): string;

    public static function url(): string;
}
