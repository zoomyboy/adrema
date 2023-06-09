<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    /** @var array<int, string> */
    public array $modules;

    public bool $single_view;

    /** @var array<int, int> */
    public array $allowed_nami_accounts;

    public static function group(): string
    {
        return 'general';
    }

    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules);
    }
}
