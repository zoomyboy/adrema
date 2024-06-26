<?php

namespace App\Fileshare\Actions;

use App\Fileshare\Models\FileshareConnection;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FileshareStoreAction
{
    use AsAction;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255|exclude',
            'config' => 'array|exclude',
        ];
    }

    public function asController(ActionRequest $request)
    {

        $type = $request->input('type')::from($request->input('config'));

        if (!$type->check()) {
            throw ValidationException::withMessages(['type' => 'Verbindung fehlgeschlagen']);
        }

        FileshareConnection::create([
            ...$request->validated(),
            'type' => $type,
        ]);
    }
}
