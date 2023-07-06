<?php

namespace App\Invoice;

use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingSaveAction
{
    use AsAction;

    /**
     * @param array<string, string> $input
     */
    public function handle(array $input): void
    {
        $settings = app(InvoiceSettings::class);

        $settings->fill([
            'from_long' => $input['from_long'] ?? '',
            'from' => $input['from'] ?? '',
            'mobile' => $input['mobile'] ?? '',
            'email' => $input['email'] ?? '',
            'website' => $input['website'] ?? '',
            'address' => $input['address'] ?? '',
            'place' => $input['place'] ?? '',
            'zip' => $input['zip'] ?? '',
            'iban' => $input['iban'] ?? '',
            'bic' => $input['bic'] ?? '',
        ]);

        $settings->save();
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->all());

        return redirect()->back();
    }
}
