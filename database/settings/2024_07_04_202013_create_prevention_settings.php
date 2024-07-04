<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('prevention.formmail', ['time' => 1, 'blocks' => []]);
    }
};
