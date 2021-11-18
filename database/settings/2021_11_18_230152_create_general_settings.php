<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{

    /**
     * @param string $mode
     * @return array<string, string|array>
     */
    public function defaults(string $mode): array
    {
        $defaults = [
            'diözese' => [
                'modes' => []
            ],
            'stamm' => [
                'modes' => ['bill']
            ]
        ];

        return $defaults[$mode];
    }

    public function up(): void
    {
        $defaults = $this->defaults(config('app.mode'));
        $this->migrator->add('general.modes', $defaults['modes']);
    }
}
