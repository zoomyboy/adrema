<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;

abstract class LocalSettings extends Settings
{
    abstract public static function title(): string;

    public static function url(): string
    {
        return '/setting/' . static::group();
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function viewData(): array;
}
