<?php

namespace App\Initialize\Actions;

use App\Initialize\InitializeJob;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class InitializeAction
{
    use AsAction;

    public function handle(int $mglnr, string $password, int $groupId)
    {
        InitializeJob::dispatch();
    }

    public function rules(): array
    {
        return [
            'mglnr' => 'required|numeric',
            'password' => 'required|string',
            'group_id' => 'required|numeric',
        ];
    }

    public function asController(ActionRequest $request, NamiSettings $settings): RedirectResponse
    {
        $settings->mglnr = $request->input('mglnr');
        $settings->password = $request->input('password');
        $settings->default_group_id = $request->input('group_id');
        $settings->save();

        $this->handle(
            (int) $request->input('mglnr', 0),
            (string) $request->input('password', ''),
            (int) $request->input('group_id', 0)
        );

        return redirect()->route('home');
    }
}
