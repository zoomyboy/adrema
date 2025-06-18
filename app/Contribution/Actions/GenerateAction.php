<?php

namespace App\Contribution\Actions;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\ContributionFactory;
use App\Contribution\Requests\GenerateRequest;
use App\Rules\JsonBase64Rule;
use Illuminate\Support\Facades\Validator;
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

    public function asController(GenerateRequest $request): BaseCompiler
    {
        $type = $request->type();
        ValidateAction::validateType($type);
        Validator::make($request->payload(), app(ContributionFactory::class)->rules($type))->validate();

        return $this->handle($request);
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
