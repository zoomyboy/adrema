<?php

namespace App\Prevention;

use App\Lib\Editor\EditorData;
use App\Setting\LocalSettings;

class PreventionSettings extends LocalSettings
{

    public EditorData $formmail;
    public EditorData $yearlymail;

    public static function group(): string
    {
        return 'prevention';
    }

    public static function title(): string
    {
        return 'Prävention';
    }

    /**
     * @inheritdoc
     */
    public function viewData(): array
    {
        return [];
    }
}
