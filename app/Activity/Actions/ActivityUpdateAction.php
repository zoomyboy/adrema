<?php

namespace App\Activity\Actions;

use App\Activity;
use App\Subactivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ActivityUpdateAction
{
    use AsAction;

    public function handle(Activity $activity, array $payload): void
    {
        DB::transaction(function() use ($activity, $payload) {
            $activity->update($payload);
            $activity->subactivities()->sync($payload['subactivities']);
        });
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'subactivities' => 'present|array'
        ];
    }

    public function asController(ActionRequest $request, Activity $activity): RedirectResponse
    {
        if ($activity->hasNami) {
            $this->validateNami($activity, $request->validated());
        }

        $this->handle($activity, $request->validated());

        return redirect()->route('activity.index');
    }

    /**
     * @todo handle this with a model event on the pivot model
     */
    private function validateNami(Activity $activity, array $payload): void
    {
        if ($activity->name !== data_get($payload, 'name', '')) {
            throw ValidationException::withMessages(['nami_id' => 'Aktivität ist in NaMi. Update des Namens nicht möglich.']);
        }

        $removingSubactivities = $activity->subactivities()->whereNotIn('id', data_get($payload, 'subactivities'))->pluck('id');

        if ($removingSubactivities->first(fn ($subactivity) => Subactivity::find($subactivity)->hasNami)) {
            throw ValidationException::withMessages(['nami_id' => 'Untertätigkeit kann nicht entfernt werden.']);
        }

        $addingSubactivities = collect(data_get($payload, 'subactivities'))->filter(fn ($subactivityId) => $activity->subactivities->doesntContain($subactivityId));

        if ($addingSubactivities->first(fn ($subactivity) => Subactivity::find($subactivity)->hasNami)) {
            throw ValidationException::withMessages(['nami_id' => 'Untertätigkeit kann nicht hinzugefügt werden.']);
        }
    }
}
