<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateNamiSettingsGroup extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('nami.default_group_id', 0);
    }
}
