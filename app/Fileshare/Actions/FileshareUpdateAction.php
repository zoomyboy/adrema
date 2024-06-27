<?php

namespace App\Fileshare\Actions;

use App\Fileshare\Models\FileshareConnection;
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

    public function handle(ActionRequest $request, FileshareConnection $fileshare): void
    {
        $type = $request->input('type')::from($request->input('config'));

        if (!$type->check()) {
            throw ValidationException::withMessages(['type' => 'Verbindung fehlgeschlagen']);
        }

        $fileshare->update([
            ...$request->validated(),
            'type' => $type,
        ]);
    }
}
