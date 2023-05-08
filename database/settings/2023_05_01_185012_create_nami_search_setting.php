<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateNamiSearchSetting extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('nami.search_params', []);
    }
}
