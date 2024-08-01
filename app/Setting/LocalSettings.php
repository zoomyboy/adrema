<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;

abstract class LocalSettings extends Settings
{
    abstract public static function title(): string;

    public function url(): string
    {
        return route('setting.view', ['settingGroup' => $this->group()]);
    }

    /**
     * @return array<string, mixed>
     */
    abstract public function viewData(): array;
}
