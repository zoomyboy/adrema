<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateNamiSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('nami.mglnr', (int) env('NAMI_MGLNR'));
        $this->migrator->add('nami.password', env('NAMI_PASSWORD'));
    }
}
