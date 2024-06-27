<?php

namespace App\Fileshare;

use App\Fileshare\Actions\FileshareConnectionIndexAction;
use App\Setting\Contracts\Indexable;
use App\Setting\LocalSettings;

class FileshareSettings extends LocalSettings implements Indexable
{
    public static function group(): string
    {
        return 'fileshare';
    }

    public static function slug(): string
    {
        return 'fileshare';
    }

    public static function indexAction(): string
    {
        return FileshareConnectionIndexAction::class;
    }

    public static function title(): string
    {
        return 'Datei-Verbindungen';
    }
}
