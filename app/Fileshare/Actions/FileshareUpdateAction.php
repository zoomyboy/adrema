<?php

namespace App\Fileshare\Actions;

use App\Fileshare\Models\Fileshare;
use App\Lib\Events\Succeeded;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FileshareUpdateAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255|exclude',
            'config' => 'array|exclude',
        ];
    }

    public function handle(ActionRequest $request, Fileshare $fileshare): void
    {
        $type = $request->input('type')::from($request->input('config'));

        if (!$type->check()) {
            throw ValidationException::withMessages(['type' => 'Verbindung fehlgeschlagen']);
        }

        $fileshare->update([
            ...$request->validated(),
            'type' => $type,
        ]);

        Succeeded::message('Verbindung bearbeitet.')->dispatch();
    }
}
