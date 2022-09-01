<?php

namespace App\Initialize\Actions;

use App\Initialize\InitializeJob;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Nami;

class InitializeAction
{
    use AsAction;

    public function handle(int $mglnr, string $password, int $groupId): void
    {
        InitializeJob::dispatch();
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'mglnr' => 'required|numeric',
            'password' => 'required|string',
            'group_id' => 'required|numeric',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        return [
            'group_id' => 'Gruppierungsnr',
        ];
    }

    public function asController(ActionRequest $request, NamiSettings $settings): RedirectResponse
    {
        $api = Nami::login($request->input('mglnr'), $request->input('password'));

        if (!$api->hasGroup($request->input('group_id'))) {
            throw ValidationException::withMessages(['nami' => 'Gruppierung nicht gefunden.']);
        }

        $settings->mglnr = $request->input('mglnr');
        $settings->password = $request->input('password');
        $settings->default_group_id = $request->input('group_id');
        $settings->save();

        $this->handle(
            (int) $request->input('mglnr', 0),
            (string) $request->input('password', ''),
            (int) $request->input('group_id', 0)
        );

        return redirect()->route('home')->success('Initialisierung beauftragt. Wir benachrichtigen dich per Mail wenn alles fertig ist.');
    }
}
