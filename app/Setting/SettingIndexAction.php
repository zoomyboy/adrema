<?php

namespace App\Setting;

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
            'bill_from_long' => $settings->from_long,
            'bill_from' => $settings->from,
            'bill_mobile' => $settings->mobile,
            'bill_email' => $settings->email,
            'bill_website' => $settings->website,
            'bill_address' => $settings->address,
            'bill_place' => $settings->place,
            'bill_zip' => $settings->zip,
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
