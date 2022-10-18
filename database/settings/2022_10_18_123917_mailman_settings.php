<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class MailmanSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mailman.base_url', null);
        $this->migrator->add('mailman.username', null);
        $this->migrator->add('mailman.password', null);
    }
}
