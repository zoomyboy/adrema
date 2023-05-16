<?php

namespace App\Contribution\Actions;

use App\Contribution\Documents\ContributionDocument;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class GenerateApiAction
{
    use AsAction;

    /**
     * @param class-string<ContributionDocument> $document
     * @param array<string, mixed>               $payload
     */
    public function handle(string $document, array $payload): BaseCompiler
    {
        return Tex::compile($document::fromApiRequest($payload));
    }

    public function asController(ActionRequest $request): BaseCompiler
    {
        ValidateAction::validateType($request->input('type'));

        return $this->handle($request->input('type'), $request->input());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
