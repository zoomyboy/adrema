<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;

class BillSettings extends Settings
{
    public string $from_long;

    public string $from;

    public string $mobile;

    public string $email;

    public string $website;

    public string $address;

    public string $place;

    public string $zip;

    public static function group(): string
    {
        return 'bill';
    }
}
