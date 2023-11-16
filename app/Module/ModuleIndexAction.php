<?php

namespace App\Module;

use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ModuleIndexAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function handle(ModuleSettings $settings): array
    {
        return [
            'data' => [
                'modules' => $settings->modules,
            ],
            'meta' => ['modules' => Module::forSelect()],
        ];
    }

    public function asController(ModuleSettings $settings): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Module');

        return Inertia::render('setting/Module', [
            'data' => $this->handle($settings),
        ]);
    }
}
