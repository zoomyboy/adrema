<?php

namespace App\Initialize\Actions;

use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Nami;

class NamiSearchAction
{
    use AsAction;

    public function handle(Api $api, int $page, array $params)
    {
        return $api->pageSearch($params, $page, 10)->toArray();
    }

    public function rules(): array
    {
        return [
            'mglnr' => 'required|numeric|min:0',
            'password' => 'required|string',
            'params' => 'array',
        ];
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        $api = Nami::login($request->input('mglnr'), $request->input('password'));

        return response()->json($this->handle($api, $request->input('page', 1), $request->input('params')));
    }
}
