<?php

namespace App\Form\Actions;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\ContributionFactory;
use App\Form\Models\Form;
use App\Form\Requests\FormCompileRequest;
use App\Rules\JsonBase64Rule;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\Tex\BaseCompiler;
use Zoomyboy\Tex\Tex;

class GenerateContributionAction
{
    use AsAction;

    public function handle(HasContributionData $request): BaseCompiler
    {
        return Tex::compile($request->type()::fromPayload($request));
    }

    public function asController(ActionRequest $request, Form $form): BaseCompiler|JsonResponse
    {
        $r = FormCompileRequest::from(['form' => $form]);
        app(ContributionFactory::class)->validateType($r);
        $r->validateContribution();

        return $request->input('validate')
            ? response()->json([])
            : $this->handle($r);
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
