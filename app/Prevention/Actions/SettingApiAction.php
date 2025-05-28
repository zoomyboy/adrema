<?php

namespace App\Prevention\Actions;

use App\Prevention\PreventionSettings;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingApiAction
{
    use AsAction;

    public function handle(): JsonResponse
    {
        return response()->json([
            'data' => app(PreventionSettings::class)->toFrontend(),
        ]);
    }
}
