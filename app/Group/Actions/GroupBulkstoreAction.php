<?php

namespace App\Group\Actions;

use App\Group;
use App\Group\Enums\Level;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GroupBulkstoreAction
{
    use AsAction;

    /**
     * @return array<string, string|array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            '*.id' => 'required|integer|exists:groups,id',
            '*.inner_name' => 'required|string|max:255',
            '*.level' => ['required', 'string', Rule::in(Level::values())],
        ];
    }

    /**
     * @param array<array-key, mixed> $groups
     */
    public function handle(array $groups): void
    {
        foreach ($groups as $payload) {
            Group::find($payload['id'])->update(['level' => $payload['level'], 'inner_name' => $payload['inner_name']]);
        }
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $this->handle($request->validated());

        return response()->json([]);
    }
}
