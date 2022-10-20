<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;

abstract class LocalSettings extends Settings
{
    abstract public static function slug(): string;

    /**
     * @return class-string
     */
    abstract public static function indexAction(): string;

    /**
     * @return class-string
     */
    abstract public static function saveAction(): string;

    abstract public static function title(): string;

    public static function url(): string
    {
        return '/setting/'.static::slug();
    }
}
