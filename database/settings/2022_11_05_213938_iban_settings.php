<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class IbanSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('bill.iban', '');
        $this->migrator->add('bill.bic', '');
    }
}
