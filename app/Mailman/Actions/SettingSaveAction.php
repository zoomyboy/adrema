<?php

namespace App\Mailman\Actions;

use App\Mailman\MailmanSettings;
use App\Mailman\Support\MailmanService;
use Illuminate\Contracts\Validation\Validator;
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
        $settings = app(MailmanSettings::class);

        $settings->fill([
            'base_url' => $input['base_url'] ?? null,
            'username' => $input['username'] ?? null,
            'password' => $input['password'] ?? null,
        ]);

        $settings->save();
    }

    public function afterValidator(Validator $validator, ActionRequest $request): void
    {
        if (!app(MailmanService::class)->setCredentials($request->input('base_url'), $request->input('username'), $request->input('password'))->check()) {
            $validator->errors()->add('mailman', 'Verbindung fehlgeschlagen.');
        }
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->all());

        return redirect()->back()->success('Einstellungen gespeichert');
    }
}
