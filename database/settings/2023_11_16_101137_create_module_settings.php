<?php

use App\Module\Module;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->delete('general.modules');
        $this->migrator->delete('general.single_view');
        $this->migrator->add('module.modules', collect(Module::cases())->map(fn ($module) => $module->value));
    }
};
