<?php

namespace App\Invoice;

use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingIndexAction
{
    use AsAction;

    /**
     * @return array<string, string>
     */
    public function handle(InvoiceSettings $settings): array
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
            'iban' => $settings->iban,
            'bic' => $settings->bic,
            'remember_weeks' => $settings->rememberWeeks,
        ];
    }

    public function asController(InvoiceSettings $settings): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Rechnungs-Einstellungen');

        return Inertia::render('setting/Bill', [
            'data' => $this->handle($settings),
        ]);
    }
}
