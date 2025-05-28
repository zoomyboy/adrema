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

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'formmail' => 'array',
            'yearlymail' => 'array',
            'weeks' => 'required|numeric|gte:0',
            'freshRememberInterval' => 'required|numeric|gte:0',
            'active' => 'boolean',
        ];
    }

    public function handle(ActionRequest $request): void
    {
        $settings = app(PreventionSettings::class);
        $settings->formmail = EditorData::from($request->formmail);
        $settings->yearlymail = EditorData::from($request->yearlymail);
        $settings->weeks = $request->weeks;
        $settings->freshRememberInterval = $request->freshRememberInterval;
        $settings->active = $request->active;
        $settings->save();

        Succeeded::message('Einstellungen gespeichert.')->dispatch();
    }
}
