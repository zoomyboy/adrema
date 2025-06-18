<?php

namespace App\Contribution\Actions;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\Requests\GenerateApiRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class GenerateApiAction
{
    use AsAction;

    /**
     * @todo merge this with GenerateAction
     */
    public function handle(HasContributionData $request): BaseCompiler
    {
        return Tex::compile($request->type()::fromPayload($request));
    }

    public function asController(GenerateApiRequest $request): BaseCompiler
    {
        $type = $request->type();
        ValidateAction::validateType($type);

        return $this->handle($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
