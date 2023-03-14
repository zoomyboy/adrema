<?php

namespace App\Contribution\Actions;

use App\Contribution\ContributionFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ValidateAction
{
    use AsAction;

    public function asController(): JsonResponse
    {
        return response()->json(['valid' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return app(ContributionFactory::class)->rules(request()->type);
    }

    public function prepareForValidation(ActionRequest $request): void
    {
        static::validateType($request->input('type'));
    }

    public static function validateType(?string $type = null): void
    {
        Validator::make(['type' => $type], app(ContributionFactory::class)->typeRule())->validate();
    }
}
