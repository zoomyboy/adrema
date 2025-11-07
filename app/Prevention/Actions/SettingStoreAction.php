<?php

namespace App\Prevention\Actions;

use App\Lib\Editor\EditorData;
use App\Lib\Events\Succeeded;
use App\Member\FilterScope;
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
            'replyToMail' => 'nullable|string|email',
        ];
    }

    public function handle(ActionRequest $request): void
    {
        $settings = app(PreventionSettings::class);
        $settings->formmail = EditorData::from($request->formmail);
        $settings->yearlymail = EditorData::from($request->yearlymail);
        $settings->weeks = $request->weeks;
        $settings->replyToMail = $request->replyToMail;
        $settings->freshRememberInterval = $request->freshRememberInterval;
        $settings->active = $request->active;
        $settings->yearlyMemberFilter = FilterScope::from($request->yearlyMemberFilter);
        $settings->preventAgainst = $request->preventAgainst;
        $settings->save();

        Succeeded::message('Einstellungen gespeichert.')->dispatch();
    }
}
