<?php

namespace App\Payment\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\In;
use Lorisleiva\Actions\Concerns\AsAction;

class PaymentUpdateAction
{
    use AsAction;

    public function handle(): void
    {
    }

    /**
     * @return array<string, array<int, string|In>>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        return [];
    }

    public function asController(): JsonResponse
    {
        return response()->json([]);
    }
}
