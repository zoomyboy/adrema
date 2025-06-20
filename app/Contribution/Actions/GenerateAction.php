<?php

namespace App\Contribution\Actions;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\ContributionFactory;
use App\Contribution\Requests\GenerateRequest;
use App\Rules\JsonBase64Rule;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class GenerateAction
{
    use AsAction;

    public function handle(HasContributionData $request): BaseCompiler
    {
        return Tex::compile($request->type()::fromPayload($request));
    }

    public function asController(GenerateRequest $request): BaseCompiler|JsonResponse
    {
        app(ContributionFactory::class)->validateType($request);
        $request->validateContribution();

        return $request->input('validate')
            ? response()->json([])
            : $this->handle($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payload' => [new JsonBase64Rule()],
        ];
    }
}
