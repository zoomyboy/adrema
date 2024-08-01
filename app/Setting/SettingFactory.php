<?php

namespace App\Setting;

use App\Invoice\InvoiceSettings;
use App\Setting\Contracts\Storeable;
use Illuminate\Routing\Router;

class SettingFactory
{
    /**
     * @var array<int, class-string<LocalSettings>>
     */
    private array $settings = [];

    /**
     * @param class-string<LocalSettings> $setting
     */
    public function register(string $setting): void
    {
        $this->settings[] = $setting;

        if (new $setting instanceof Storeable) {
            $this->registerStoreRoute(new $setting);
        }

        if (1 === count($this->settings)) {
            app(Router::class)->redirect('/setting', '/setting/' . $setting::group());
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

    public function resolveGroupName(string $name): LocalSettings
    {
        $settingClass = collect($this->settings)->first(fn ($setting) => $setting::group() === $name);

        return app($settingClass);
    }

    protected function registerStoreRoute(Storeable $setting): void
    {
        app(Router::class)->middleware(['web', 'auth:web'])->post($setting::url(), $setting::storeAction());
    }
}
