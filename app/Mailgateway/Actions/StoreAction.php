<?php

namespace App\Mailgateway\Actions;

use App\Mailgateway\Models\Mailgateway;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreAction
{
    use AsAction;

    public function handle(array $input)
    {
        if (!(new $input['type']['cls']($input['type']['params']))->works()) {
            throw ValidationException::withMessages(['erorr' => 'Verbindung fehlgeschlagen.']);
        }

        Mailgateway::create($input);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'type.cls' => ['required', 'string', 'max:255', Rule::in(app('mail-gateways'))],
            ...collect(request()->input('type.cls')::rules('storeValidator'))->mapWithKeys(fn ($rules, $key) => ["type.params.{$key}" => $rules]),
        ];
    }

    public function asController(ActionRequest $request): void
    {
        $this->handle($request->validated());
    }
}
