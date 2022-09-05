<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class BillSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('bill.from_long', '');
        $this->migrator->add('bill.from', '');
        $this->migrator->add('bill.mobile', '');
        $this->migrator->add('bill.email', '');
        $this->migrator->add('bill.website', '');
        $this->migrator->add('bill.address', '');
        $this->migrator->add('bill.place', '');
        $this->migrator->add('bill.zip', '');
    }
}
