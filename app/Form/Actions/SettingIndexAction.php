<?php

namespace App\Form\Actions;

use App\Form\FormSettings;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingIndexAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function handle(FormSettings $settings): array
    {
        return [
            'data' => [
                'register_url' => $settings->registerUrl,
            ],
        ];
    }

    public function asController(FormSettings $settings): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Module');

        return Inertia::render('setting/Form', [
            'data' => $this->handle($settings),
        ]);
    }
}
