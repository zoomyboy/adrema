<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class MailmanIsActive extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mailman.is_active', false);
    }
}
