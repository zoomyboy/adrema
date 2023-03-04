<?php

namespace App\Activity\Actions;

use App\Activity;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ActivityStoreAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $payload
     */
    public function handle(array $payload): Activity
    {
        $activity = Activity::create($payload);

        $activity->subactivities()->sync($payload['subactivities']);

        return $activity;
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
        ];
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->validated());

        return redirect()->route('activity.index');
    }
}
