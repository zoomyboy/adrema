<?php

namespace App\Form\Actions;

use App\Form\FormSettings;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SettingStoreAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $input
     */
    public function handle(array $input): void
    {
        $settings = app(FormSettings::class);

        $settings->fill([
            'registerUrl' => $input['register_url'],
            'clearCacheUrl' => $input['clear_cache_url'],
        ]);

        $settings->save();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'register_url' => 'present|string',
            'clear_cache_url' => 'present|string',
        ];
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        $this->handle($request->validated());

        return redirect()->back();
    }
}
