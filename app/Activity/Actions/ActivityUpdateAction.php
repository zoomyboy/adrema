<?php

namespace App\Activity\Actions;

use App\Activity;
use App\Member\Membership;
use App\Subactivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * @template Payload of array{name: string, subactivities: array<int, int>}
 */
class ActivityUpdateAction
{
    use AsAction;

    /**
     * @param Payload $payload
     */
    public function handle(Activity $activity, array $payload): void
    {
        DB::transaction(function () use ($activity, $payload) {
            $activity->update($payload);
            $activity->subactivities()->sync($payload['subactivities']);
        });
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'is_filterable' => 'present|boolean',
            'subactivities' => 'present|array',
            'subactivities.*' => 'integer',
        ];
    }

    public function asController(ActionRequest $request, Activity $activity): RedirectResponse
    {
        if ($activity->hasNami) {
            $this->validateNami($activity, $request->validated());
        }

        $removingSubactivities = $activity->subactivities()->whereNotIn('id', $request->validated('subactivities'))->pluck('id');

        if ($removingSubactivities->first(fn ($subactivity) => Membership::where(['activity_id' => $activity->id, 'subactivity_id' => $subactivity])->exists())) {
            throw ValidationException::withMessages(['subactivities' => 'Untergliederung hat noch Mitglieder.']);
        }

        $this->handle($activity, $request->validated());

        return redirect()->route('activity.index');
    }

    /**
     * @todo handle this with a model event on the pivot model
     *
     * @param Payload $payload
     */
    private function validateNami(Activity $activity, array $payload): void
    {
        if ($activity->name !== $payload['name']) {
            throw ValidationException::withMessages(['nami_id' => 'Aktivität ist in NaMi. Update des Namens nicht möglich.']);
        }

        $removingSubactivities = $activity->subactivities()->whereNotIn('id', $payload['subactivities'])->pluck('id');

        if ($removingSubactivities->first(fn ($subactivity) => Subactivity::find($subactivity)->hasNami)) {
            throw ValidationException::withMessages(['nami_id' => 'Untertätigkeit kann nicht entfernt werden.']);
        }

        $addingSubactivities = collect($payload['subactivities'])->filter(fn ($subactivityId) => $activity->subactivities->doesntContain($subactivityId));

        if ($addingSubactivities->first(fn ($subactivity) => Subactivity::find($subactivity)->hasNami)) {
            throw ValidationException::withMessages(['nami_id' => 'Untertätigkeit kann nicht hinzugefügt werden.']);
        }
    }
}
