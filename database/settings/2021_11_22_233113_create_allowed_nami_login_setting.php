<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateAllowedNamiLoginSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.allowed_nami_accounts', []);
    }
}
