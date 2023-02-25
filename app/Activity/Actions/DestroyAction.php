<?php

namespace App\Activity\Actions;

use App\Activity;
use App\Member\Membership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyAction
{
    use AsAction;

    public function handle(Activity $activity): void
    {
        $activity->subactivities()->sync([]);
        $activity->delete();
    }

    public function asController(Activity $activity): RedirectResponse
    {
        if (Membership::where('activity_id', $activity->id)->count()) {
            throw ValidationException::withMessages(['activity' => 'Tätigkeit besitzt noch Mitglieder.']);
        }

        if ($activity->hasNami) {
            throw ValidationException::withMessages(['activity' => 'Tätigkeit ist in NaMi.']);
        }

        $this->handle($activity);

        return redirect()->route('activity.index');
    }
}
