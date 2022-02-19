<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;

class NamiSettings extends Settings
{

    public int $mglnr;

    public string $password;

    public static function group(): string
    {
        return 'nami';
    }

}
