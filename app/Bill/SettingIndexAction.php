<?php

namespace App\Bill;

use App\Setting\BillSettings;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingIndexAction
{
    use AsAction;

    /**
     * @return array<string, string>
     */
    public function handle(BillSettings $settings): array
    {
        return [
            'from_long' => $settings->from_long,
            'from' => $settings->from,
            'mobile' => $settings->mobile,
            'email' => $settings->email,
            'website' => $settings->website,
            'address' => $settings->address,
            'place' => $settings->place,
            'zip' => $settings->zip,
        ];
    }

    public function asController(BillSettings $settings): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Einstellungen');

        return Inertia::render('setting/Index', [
            'data' => $this->handle($settings),
        ]);
    }
}
