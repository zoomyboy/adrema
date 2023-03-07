<?php

namespace App\Activity\Api;

use App\Resources\SubactivityResource;
use App\Subactivity;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class SubactivityShowAction
{
    use AsAction;

    public function handle()
    {
        // ...
    }

    public function asController(Subactivity $subactivity): JsonResponse
    {
        return response()->json([
            'data' => new SubactivityResource($subactivity),
            'meta' => SubactivityResource::meta(),
        ]);
    }
}
