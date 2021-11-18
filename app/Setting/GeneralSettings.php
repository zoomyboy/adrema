<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    /** @var array<int, string> */
    public array $modes;

    /**
     * @return array<int, string>
     */
    public function modeOptions(): array
    {
        return [
            'bill',
        ];
    }

    public static function group(): string
    {
        return 'general';
    }

}
