<?php

namespace App\Prevention\Actions;

use App\Lib\Editor\EditorData;
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
        $settings = app(PreventionSettings::class);
        $settings->formmail = EditorData::from($request->formmail);
        $settings->save();

        Succeeded::message('Einstellungen gespeichert.')->dispatch();
    }
}
