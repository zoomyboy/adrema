<?php

namespace App\Setting\Actions;

use App\Setting\Contracts\Storeable;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $input
     */
    public function handle(Storeable $settings, array $input): void
    {
        $settings->fill($input)->save();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Storeable */
        $group = request()->route('settingGroup');

        return $group->rules();
    }

    public function asController(ActionRequest $request, Storeable $settingGroup): RedirectResponse
    {
        $settingGroup->beforeSave($request);
        $this->handle($settingGroup, $request->validated());

        return redirect()->back();
    }
}
