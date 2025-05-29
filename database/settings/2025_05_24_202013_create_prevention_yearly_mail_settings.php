<?php

use App\Member\FilterScope;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('prevention.yearlymail', ['time' => 1, 'blocks' => [], 'version' => '1.0']);
        $this->migrator->add('prevention.weeks', 8);
        $this->migrator->add('prevention.freshRememberInterval', 12);
        $this->migrator->add('prevention.active', false);
        $this->migrator->add('prevention.yearlyMemberFilter', FilterScope::from([])->toArray());
        $this->migrator->add('prevention.preventAgainst', []);
    }
};
