<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('prevention.yearlymail', ['time' => 1, 'blocks' => [], 'version' => '1.0']);
    }
};
