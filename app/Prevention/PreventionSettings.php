<?php

namespace App\Prevention;

use App\Lib\Editor\EditorData;
use App\Member\FilterScope;
use App\Setting\LocalSettings;

class PreventionSettings extends LocalSettings
{

    public EditorData $formmail;
    public EditorData $yearlymail;
    public int $weeks;
    public int $freshRememberInterval;
    public bool $active;
    public FilterScope $yearlyMemberFilter;

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

    /**
     * @todo return int value here and handle this in vue with a number field that only expects integers
     * @return array<string, mixed>
     */
    public function toFrontend(): array
    {
        return [
            ...$this->toArray(),
            'weeks' => (string) $this->weeks,
            'freshRememberInterval' => (string) $this->freshRememberInterval,
        ];
    }
}
