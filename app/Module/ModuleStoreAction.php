<?php

namespace App\Module;

use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ModuleStoreAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $input
     */
    public function handle(array $input): void
    {
        $settings = app(ModuleSettings::class);

        $settings->fill([
            'modules' => $input['modules'],
        ]);

        $settings->save();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'modules' => 'present|array',
            'modules.*' => ['string', Rule::in(Module::values())],
        ];
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->validated());

        return redirect()->back();
    }
}
