<?php

namespace App\Membership\Actions;

use App\Member\Membership;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ListForGroupAction
{
    use AsAction;

    public function asController(ActionRequest $request): JsonResponse
    {
        return response()->json(Membership::active()->where([
            'group_id' => $request->group_id,
            'activity_id' => $request->activity_id,
            'subactivity_id' => $request->subactivity_id,
        ])->pluck('member_id'));
    }
}
