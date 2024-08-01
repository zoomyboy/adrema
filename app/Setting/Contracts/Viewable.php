<?php

namespace App\Setting\Contracts;

interface Viewable
{
    /**
     * @return class-string
     */
    public static function indexAction(): string;
}
