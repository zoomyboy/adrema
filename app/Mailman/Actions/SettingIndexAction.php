<?php

namespace App\Mailman\Actions;

use App\Mailman\MailmanSettings;
use App\Mailman\Support\MailmanService;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingIndexAction
{
    use AsAction;

    /**
     * @return array<string, string|bool|null>
     */
    public function handle(MailmanSettings $settings): array
    {
        return [
            'is_active' => $settings->is_active,
            'base_url' => $settings->base_url,
            'username' => $settings->username,
            'password' => '',
        ];
    }

    public function asController(MailmanSettings $settings): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Mailman-Einstellungen');

        $state = $settings->base_url && $settings->username && $settings->password && $settings->is_active
            ? app(MailmanService::class)->setCredentials($settings->base_url, $settings->username, $settings->password)->check()
            : null;

        return Inertia::render('setting/Mailman', [
            'data' => $this->handle($settings),
            'state' => $state,
        ]);
    }
}
