<?php

namespace App\Initialize\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MemberEntry;
use Zoomyboy\LaravelNami\Nami;

class NamiSearchAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $params
     *
     * @return LengthAwarePaginator<MemberEntry>
     */
    public function handle(Api $api, int $page, array $params, int $perPage = 10): LengthAwarePaginator
    {
        return $api->pageSearch($params, $page, $perPage);
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'mglnr' => 'required|numeric|min:0',
            'password' => 'required|string',
            'params' => 'array',
        ];
    }

    /**
     * @return LengthAwarePaginator<MemberEntry>
     */
    public function asController(ActionRequest $request): LengthAwarePaginator
    {
        $api = Nami::login($request->input('mglnr'), $request->input('password'));

        return $this->handle($api, $request->input('page', 1), $request->input('params'));
    }
}
