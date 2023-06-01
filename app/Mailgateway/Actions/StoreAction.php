<?php

namespace App\Mailgateway\Actions;

use App\Mailgateway\Models\Mailgateway;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreAction
{
    use AsAction;

    public function handle(array $input)
    {
        Mailgateway::create($input);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'type.class' => ['required', 'string', 'max:255', Rule::in(app('mail-gateways'))],
            'type.params' => 'present',
        ];
    }

    public function asController(ActionRequest $request): void
    {
        $this->handle($request->validated());
    }
}
