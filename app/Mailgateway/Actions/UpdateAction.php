<?php

namespace App\Mailgateway\Actions;

use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Types\Type;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $input
     */
    public function handle(Mailgateway $mailgateway, array $input): void
    {
        if (!app($input['type']['cls'])->setParams($input['type']['params'])->works()) {
            throw ValidationException::withMessages(['connection' => 'Verbindung fehlgeschlagen.']);
        }

        $mailgateway->update($input);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            ...$this->typeValidation(),
            'type.params' => 'present|array',
            ...collect(request()->input('type.cls')::rules('storeValidator'))->mapWithKeys(fn ($rules, $key) => ["type.params.{$key}" => $rules]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getValidationAttributes(): array
    {
        return [
            'type.cls' => 'Typ',
            'name' => 'Beschreibung',
            'domain' => 'Domain',
            ...collect(request()->input('type.cls')::fieldNames())->mapWithKeys(fn ($attribute, $key) => ["type.params.{$key}" => $attribute]),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function typeValidation(): array
    {
        return [
            'type.cls' => ['required', 'string', 'max:255', Rule::in(app('mail-gateways'))],
        ];
    }

    public function prepareForValidation(ActionRequest $request): void
    {
        if (!is_subclass_of(request()->input('type.cls'), Type::class)) {
            throw ValidationException::withMessages(['type.cls' => 'Typ ist nicht valide.']);
        }
    }

    public function asController(Mailgateway $mailgateway, ActionRequest $request): void
    {
        $this->handle($mailgateway, $request->validated());
    }
}
