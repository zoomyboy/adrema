<?php

namespace App\Nami\Actions;

use App\Initialize\Actions\NamiLoginCheckAction;
use App\Setting\NamiSettings;
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
        $settings = app(NamiSettings::class);

        $settings->fill([
            'mglnr' => $input['mglnr'] ?? '',
            'password' => $input['password'] ?? '',
            'default_group_id' => $input['default_group_id'] ?? '',
        ]);

        $settings->save();
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        NamiLoginCheckAction::run([
            'mglnr' => $request->mglnr,
            'password' => $request->password,
        ]);

        $this->handle($request->all());

        return redirect()->back();
    }
}
