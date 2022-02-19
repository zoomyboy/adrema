<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateNamiSettings extends SettingsMigration
{

    public function up(): void
    {
        $this->migrator->add('nami.mglnr', -1);
        $this->migrator->add('nami.password', '');
    }
}
