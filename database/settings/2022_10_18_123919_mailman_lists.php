<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class MailmanLists extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('mailman.all_parents_list', null);
        $this->migrator->add('mailman.all_list', null);
        $this->migrator->add('mailman.active_leaders_list', null);
        $this->migrator->add('mailman.passive_leaders_list', null);
    }
}
