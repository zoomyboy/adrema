<?php

namespace App\Module;

use App\Setting\Contracts\Viewable;
use App\Setting\Contracts\Storeable;
use App\Setting\LocalSettings;

class ModuleSettings extends LocalSettings implements Viewable, Storeable
{
    /** @var array<int, string> */
    public array $modules;

    public static function group(): string
    {
        return 'module';
    }

    public static function slug(): string
    {
        return 'module';
    }

    public static function title(): string
    {
        return 'Module';
    }

    public static function storeAction(): string
    {
        return ModuleStoreAction::class;
    }

    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules);
    }

    /**
     * @inheritdoc
     */
    public function viewData(): array
    {
        return [
            'data' => [
                'data' => [
                    'modules' => $this->modules,
                ],
                'meta' => ['modules' => Module::forSelect()],
            ]
        ];
    }
}
