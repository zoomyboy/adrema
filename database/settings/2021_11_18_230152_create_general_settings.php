<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    /**
     * @return array<string, array<int,string>|bool>
     */
    public function defaults(string $mode): array
    {
        $defaults = [
            'diözese' => [
                'modules' => ['courses'],
                'single_view' => false,
            ],
            'stamm' => [
                'modules' => ['bill', 'courses'],
                'single_view' => true,
            ],
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
