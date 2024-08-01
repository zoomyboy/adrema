<?php

namespace App\Setting;

use App\Invoice\InvoiceSettings;
use App\Setting\Contracts\Storeable;
use App\Setting\Contracts\Viewable;
use Illuminate\Routing\Router;

class SettingFactory
{
    /**
     * @var array<int, class-string<LocalSettings>>
     */
    private array $settings = [];

    /**
     * @param class-string $setting
     */
    public function register(string $setting): void
    {
        $this->settings[] = $setting;

        if (new $setting() instanceof Storeable) {
            app(Router::class)->middleware(['web', 'auth:web'])->post($setting::url(), $setting::storeAction());
        }

        if (1 === count($this->settings)) {
            app(Router::class)->redirect('/setting', '/setting/' . $setting::slug());
        }
    }

    /**
     * @return array<int, array{url: string, is_active: bool}>
     */
    public function getShare(): array
    {
        return collect($this->settings)->map(fn ($setting) => [
            'url' => $setting::url(),
            'is_active' => '/' . request()->path() === $setting::url(),
            'title' => $setting::title(),
        ])
            ->toArray();
    }

    public function resolveGroupName(string $name): Viewable
    {
        $settingClass = collect($this->settings)->filter(fn ($setting) => new $setting() instanceof Viewable)->first(fn ($setting) => $setting::group() === $name);

        return app($settingClass);
    }
}
