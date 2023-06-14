<?php

namespace App\Maildispatcher\Actions;

use App\Maildispatcher\Models\Maildispatcher;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $input
     */
    public function handle(Maildispatcher $maildispatcher, array $input): void
    {
        $maildispatcher->update([
            ...$input,
            'filter' => (object) $input['filter'],
        ]);
        ResyncAction::dispatch();
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        return [
            'gateway_id' => 'Verbindung',
        ];
    }

    public function rules(): array
    {
        return [
            'gateway_id' => 'required|exists:mailgateways,id',
            'name' => 'required|max:50',
            'filter' => 'present|array',
        ];
    }

    public function asController(Maildispatcher $maildispatcher, ActionRequest $request): JsonResponse
    {
        $this->handle($maildispatcher, $request->validated());

        return response()->json('', 201);
    }
}
