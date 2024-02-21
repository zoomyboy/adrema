<?php

namespace App\Form\Actions;

use App\Form\Models\Form;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class FormUpdateMetaAction
{
    use AsAction;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Form */
        $form = request()->route('form');

        return [
            'sorting' => 'array',
            'sorting.0' => 'required|string',
            'sorting.1' => 'required|string|in:asc,desc',
            'active_columns' => 'array',
            'active_columns.*' => ['string', Rule::in($form->getFields()->pluck('key')->toArray())]
        ];
    }

    /**
     * @param array<string, mixed> $input
     */
    public function handle(Form $form, array $input): void
    {
        $form->update(['meta' => $input]);
    }

    public function asController(Form $form, ActionRequest $request)
    {
        $this->handle($form, $request->validated());

        return response()->json($form->fresh()->meta);
    }
}
