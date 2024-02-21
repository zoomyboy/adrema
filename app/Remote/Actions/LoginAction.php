<?php

namespace App\Remote\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Authentication\Auth;

class LoginAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {

        return [
            'mglnr' => 'required|numeric',
            'password' => 'required|string',
        ];
    }

    public function handle(ActionRequest $request): JsonResponse
    {
        Auth::login($request->mglnr, $request->password);

        return response()->json([
            'access_token' => Crypt::encryptString(json_encode(['mglnr' => $request->mglnr, 'password' => $request->password])),
        ]);
    }
}
