<?php

namespace App\Mailgateway\Actions;

use App\Mailgateway\Models\Mailgateway;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAction
{
    use AsAction;
    use ValidatesRequests;

    /**
     * @param array<string, mixed> $input
     */
    public function handle(Mailgateway $mailgateway, array $input): void
    {
        $this->checkIfWorks($input);

        $mailgateway->update($input);
    }

    public function asController(Mailgateway $mailgateway, ActionRequest $request): void
    {
        $this->handle($mailgateway, $request->validated());
    }
}
