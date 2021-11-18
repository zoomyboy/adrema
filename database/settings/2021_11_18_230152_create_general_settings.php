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
                'modules' => [],
                'single_view' => false,
            ],
            'stamm' => [
                'modules' => ['bill'],
                'single_view' => true,
            ]
        ];

        return $defaults[$mode];
    }

    public function up(): void
    {
        $defaults = $this->defaults(config('app.mode'));
        $this->migrator->add('general.modules', $defaults['modules']);
        $this->migrator->add('general.single_view', $defaults['single_view']);
    }
}
