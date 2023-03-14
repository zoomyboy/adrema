<?php

namespace App\Contribution\Actions;

use App\Contribution\ContributionFactory;
use App\Contribution\Documents\DvDocument;
use App\Rules\JsonBase64Rule;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Compiler;
use Zoomyboy\Tex\Tex;

class GenerateAction
{
    use AsAction;

    /**
     * @param class-string<DvDocument> $document
     * @param array<string, mixed>     $payload
     */
    public function handle(string $document, array $payload): BaseCompiler
    {
        return Tex::compile($document::fromRequest($payload));
    }

    public function asController(ActionRequest $request): Compiler
    {
        $payload = $this->payload($request);
        $type = data_get($payload, 'type');
        ValidateAction::validateType($type);
        Validator::make($payload, app(ContributionFactory::class)->rules($type))->validate();

        return $this->handle($type, $payload);
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

    /**
     * @return array<string, string>
     */
    private function payload(ActionRequest $request): array
    {
        return json_decode(base64_decode($request->input('payload', '')), true);
    }
}
