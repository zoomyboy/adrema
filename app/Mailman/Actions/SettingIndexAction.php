<?php

namespace App\Mailman\Actions;

use App\Mailman\MailmanSettings;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingIndexAction
{
    use AsAction;

    /**
     * @return array<string, string>
     */
    public function handle(MailmanSettings $settings): array
    {
        return [
            'base_url' => $settings->base_url,
            'username' => $settings->username,
            'password' => '',
        ];
    }

    public function asController(MailmanSettings $settings): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Mailman-Einstellungen');

        return Inertia::render('setting/Index', [
            'data' => $this->handle($settings),
        ]);
    }
}
