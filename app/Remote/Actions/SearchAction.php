<?php

namespace App\Remote\Actions;

use App\Initialize\Actions\NamiSearchAction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Crypt;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Nami;

class SearchAction
{
    use AsAction;

    /**
     * @return LengthAwarePaginator<array<string, mixed>>
     */
    public function handle(ActionRequest $request): LengthAwarePaginator
    {
        $token = str($request->header('Authorization'))->replace('Bearer ', '')->toString();
        $credentials = json_decode(Crypt::decryptString($token));

        $api = Nami::login($credentials->mglnr, $credentials->password);

        $results = NamiSearchAction::run($api, $request->input('page', 1), $request->input(), 50);
        $results->transform(fn ($member) => ['id' => $member->memberId, 'name' => $member->firstname . ' ' . $member->lastname]);
        return $results;
    }
}
