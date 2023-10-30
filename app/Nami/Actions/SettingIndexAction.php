<?php

namespace App\Nami\Actions;

use App\Setting\NamiSettings;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingIndexAction
{
    use AsAction;

    /**
     * @return array<string, string>
     */
    public function handle(NamiSettings $settings): array
    {
        return [
            'mglnr' => $settings->mglnr,
            'password' => '',
            'default_group_id' => $settings->default_group_id,
        ];
    }

    public function asController(NamiSettings $settings): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'NaMi-Settings');

        return Inertia::render('setting/Nami', [
            'data' => $this->handle($settings),
        ]);
    }
}
