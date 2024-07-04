<?php

namespace App\Prevention;

use App\Lib\Editor\EditorData;
use App\Prevention\Actions\PreventionIndexAction;
use App\Setting\Contracts\Indexable;
use App\Setting\LocalSettings;

class PreventionSettings extends LocalSettings implements Indexable
{

    public EditorData $formmail;

    public static function group(): string
    {
        return 'prevention';
    }

    public static function slug(): string
    {
        return 'prevention';
    }

    public static function indexAction(): string
    {
        return PreventionIndexAction::class;
    }

    public static function title(): string
    {
        return 'Prävention';
    }
}
