<?php

namespace App\Initialize\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Data\SearchLayerOption;
use Zoomyboy\LaravelNami\Enum\SearchLayer;
use Zoomyboy\LaravelNami\Nami;

class NamiGetSearchLayerAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $input
     *
     * @return Collection<int, SearchLayerOption>
     */
    public function handle(array $input): Collection
    {
        return Nami::login((int) $input['mglnr'], $input['password'])->searchLayerOptions(
            SearchLayer::from($input['layer'] ?: 0),
            $input['parent'] ?: null
        );
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'mglnr' => 'required|numeric|min:0',
            'password' => 'required|string',
            'parent' => 'present',
            'layer' => 'required|numeric',
        ];
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $response = $this->handle($request->validated());

        return response()->json($response);
    }
}
