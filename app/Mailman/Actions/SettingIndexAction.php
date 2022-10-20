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
            'all_list' => $settings->all_list,
            'all_parents_list' => $settings->all_parents_list,
            'active_leaders_list' => $settings->active_leaders_list,
            'passive_leaders_list' => $settings->passive_leaders_list,
            'password' => '',
        ];
    }

    public function asController(MailmanSettings $settings): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Mailman-Einstellungen');

        if ($settings->is_active) {
            $state = app(MailmanService::class)->fromSettings($settings)->check();
            $lists = app(MailmanService::class)->fromSettings($settings)->getLists();
        } else {
            $state = null;
            $lists = [];
        }

        return Inertia::render('setting/Mailman', [
            'data' => $this->handle($settings),
            'state' => $state,
            'lists' => $lists,
        ]);
    }
}
