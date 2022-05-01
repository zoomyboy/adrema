<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class create_nami_settings_group extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('nami.default_group_id', (int) env('NAMI_GROUP'));
    }
}
