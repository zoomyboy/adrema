<?php

namespace App\Setting;

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

        app(Router::class)->middleware(['web', 'auth:web', SettingMiddleware::class])->get($setting::url(), $setting::indexAction());
        app(Router::class)->middleware(['web', 'auth:web', SettingMiddleware::class])->post($setting::url(), $setting::saveAction());

        if (1 === count($this->settings)) {
            app(Router::class)->redirect('/setting', '/setting/'.$setting::slug());
        }
    }

    /**
     * @return array<int, array{url: string, is_active: bool}>
     */
    public function getShare(): array
    {
        return collect($this->settings)->map(fn ($setting) => [
            'url' => $setting::url(),
            'is_active' => request()->fullUrlIs(url($setting::url())),
            'title' => $setting::title(),
        ])
        ->toArray();
    }
}
