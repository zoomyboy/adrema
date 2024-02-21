<?php

namespace App\Remote\Actions;

use App\Initialize\Actions\NamiSearchAction;
use Illuminate\Support\Facades\Crypt;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Nami;

class SearchAction
{
    use AsAction;

    public function handle(ActionRequest $request)
    {
        $token = str($request->header('Authorization'))->replace('Bearer ', '')->toString();
        $credentials = json_decode(Crypt::decryptString($token));

        $api = Nami::login($credentials->mglnr, $credentials->password);

        $results = NamiSearchAction::run($api, $request->input('page', 1), [], 50);
        return $results->map(fn ($member) => ['id' => $member->memberId, 'name' => $member->firstname . ' ' . $member->lastname]);
    }
}
