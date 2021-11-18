<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    /** @var array<int, string> */
    public array $modules;

    public bool $single_view;

    /**
     * @return array<int, string>
     */
    public function moduleOptions(): array
    {
        return [
            'bill',
        ];
    }

    public static function group(): string
    {
        return 'general';
    }

    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules);
    }

}
