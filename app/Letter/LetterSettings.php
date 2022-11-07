<?php

namespace App\Letter;

use App\Setting\LocalSettings;

class LetterSettings extends LocalSettings
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

    public static function slug(): string
    {
        return 'bill';
    }

    public static function indexAction(): string
    {
        return SettingIndexAction::class;
    }

    public static function saveAction(): string
    {
        return SettingSaveAction::class;
    }

    public static function title(): string
    {
        return 'Rechnung';
    }
}
