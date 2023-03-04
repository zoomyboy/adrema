<?php

namespace App\Activity\Api;

use App\Subactivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SubactivityStoreAction
{
    use AsAction;

    /**
     * @param array<string, string|array<int, int>> $payload
     */
    public function handle(array $payload): Subactivity
    {
        $subactivity = Subactivity::create(Arr::except($payload, 'activities'));
        $subactivity->activities()->sync($payload['activities']);

        return $subactivity;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:subactivities,name',
            'activities' => 'present|array|min:1',
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

    public function asController(ActionRequest $request): JsonResponse
    {
        return response()->json($this->handle($request->validated()));
    }
}
