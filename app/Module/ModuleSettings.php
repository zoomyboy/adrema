<?php

namespace App\Module;

use App\Setting\Contracts\Storeable;
use App\Setting\LocalSettings;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;

class ModuleSettings extends LocalSettings implements Storeable
{
    /** @var array<int, string> */
    public array $modules;

    public static function group(): string
    {
        return 'module';
    }

    public static function title(): string
    {
        return 'Module';
    }

    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules);
    }

    public function beforeSave(ActionRequest $request): void
    {
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            'modules' => 'present|array',
            'modules.*' => ['string', Rule::in(Module::values())],
        ];
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
