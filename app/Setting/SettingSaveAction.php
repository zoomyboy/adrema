<?php

namespace App\Setting;

use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingSaveAction
{
    use AsAction;

    public function handle(array $input): void
    {
        $settings = app(BillSettings::class);

        $settings->from_long = $input['bill_from_long'] ?? '';
        $settings->from = $input['bill_from'] ?? '';
        $settings->mobile = $input['bill_mobile'] ?? '';
        $settings->email = $input['bill_email'] ?? '';
        $settings->website = $input['bill_website'] ?? '';
        $settings->address = $input['bill_address'] ?? '';
        $settings->place = $input['bill_place'] ?? '';
        $settings->zip = $input['bill_zip'] ?? '';
        $settings->save();
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->all());

        return redirect()->back()->success('Einstellungen gespeichert');
    }
}
