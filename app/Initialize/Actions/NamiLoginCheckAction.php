<?php

namespace App\Initialize\Actions;

use Illuminate\Http\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Nami;

class NamiLoginCheckAction
{
    use AsAction;

    /**
     * @param array{mglnr: string, password: string} $input
     */
    public function handle(array $input): void
    {
        Nami::freshLogin((int) $input['mglnr'], $input['password']);
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'mglnr' => 'required|numeric|min:0',
            'password' => 'required|string',
        ];
    }

    public function asController(ActionRequest $request): Response
    {
        $this->handle($request->validated());

        return response()->noContent();
    }
}
