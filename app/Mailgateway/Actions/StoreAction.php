<?php

namespace App\Mailgateway\Actions;

use App\Mailgateway\Models\Mailgateway;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreAction
{
    use AsAction;
    use ValidatesRequests;

    /**
     * @param array<string, mixed> $input
     */
    public function handle(array $input): void
    {
        $this->checkIfWorks($input);
        Mailgateway::create($input);
    }

    public function asController(ActionRequest $request): void
    {
        $this->handle($request->validated());
    }
}
