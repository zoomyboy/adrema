<?php

namespace App\Prevention\Actions;

use App\Lib\Events\Succeeded;
use App\Prevention\PreventionSettings;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingStoreAction
{
    use AsAction;

    public function rules(): array
    {
        return [
            'formmail' => 'array',
        ];
    }

    public function handle(ActionRequest $request): void
    {
        app(PreventionSettings::class)->fill($request->validated())->save();

        Succeeded::message('Einstellungen gespeichert.')->dispatch();
    }
}
