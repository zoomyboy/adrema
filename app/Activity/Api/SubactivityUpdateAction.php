<?php

namespace App\Activity\Api;

use App\Activity;
use App\Member\Membership;
use App\Subactivity;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SubactivityUpdateAction
{
    use AsAction;

    /**
     * @param array<string, string|array<int, int>> $payload
     */
    public function handle(Subactivity $subactivity, array $payload): Subactivity
    {
        return DB::transaction(function () use ($subactivity, $payload) {
            $subactivity->update(Arr::except($payload, 'activities'));

            if (null !== data_get($payload, 'activities')) {
                $subactivity->activities()->sync($payload['activities']);
            }

            return $subactivity;
        });
    }

    /**
     * @return array<string, string|array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('subactivities', 'name')->ignore(request()->route('subactivity')->id)],
            'activities' => ['present', 'array', 'min:1'],
            'is_filterable' => 'present|boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        return [
            'activities' => 'Tätigkeiten',
        ];
    }

    public function asController(ActionRequest $request, Subactivity $subactivity): JsonResponse
    {
        if ($subactivity->hasNami) {
            $this->validateNami($subactivity, $request->validated());
        }

        $removingActivities = $subactivity->activities()->whereNotIn('id', $request->validated('activities'))->pluck('id');

        if ($removingActivities->first(fn ($activity) => Membership::firstWhere(['activity_id' => $activity, 'subactivity_id' => $subactivity->id]))) {
            throw ValidationException::withMessages(['activities' => 'Tätigkeit hat noch Mitglieder.']);
        }

        return response()->json($this->handle($subactivity, $request->validated()));
    }

    /**
     * @todo handle this with a model event on the pivot model
     *
     * @param Payload $payload
     */
    private function validateNami(Subactivity $subactivity, array $payload): void
    {
        if ($subactivity->name !== $payload['name']) {
            throw ValidationException::withMessages(['name' => 'Untertätigkeit ist in NaMi. Update des Namens nicht möglich.']);
        }

        $removingActivities = $subactivity->activities()->whereNotIn('id', $payload['activities'])->pluck('id');

        if ($removingActivities->first(fn ($activity) => Activity::find($activity)->hasNami)) {
            throw ValidationException::withMessages(['activities' => 'Tätigkeit kann nicht entfernt werden.']);
        }

        $addingActivities = collect($payload['activities'])->filter(fn ($activityId) => $subactivity->activities->doesntContain($activityId));

        if ($addingActivities->first(fn ($activity) => Activity::find($activity)->hasNami)) {
            throw ValidationException::withMessages(['activities' => 'Tätigkeit kann nicht hinzugefügt werden.']);
        }
    }
}
