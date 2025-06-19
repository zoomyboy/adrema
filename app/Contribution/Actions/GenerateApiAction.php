<?php

namespace App\Contribution\Actions;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\ContributionFactory;
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
        app(ContributionFactory::class)->validateType($request);

        return $this->handle($request);
    }
}
