<?php

namespace App\Form;

use App\Form\Actions\SettingStoreAction;
use App\Setting\Contracts\Storeable;
use App\Setting\LocalSettings;

class FormSettings extends LocalSettings implements Storeable
{
    public string $registerUrl;
    public string $clearCacheUrl;

    public static function group(): string
    {
        return 'form';
    }

    public static function title(): string
    {
        return 'Formulare';
    }

    public static function storeAction(): string
    {
        return SettingStoreAction::class;
    }

    /**
     * @inheritdoc
     */
    public function viewData(): array
    {
        return [
            'data' => [
                'data' => [
                    'register_url' => $this->registerUrl,
                    'clear_cache_url' => $this->clearCacheUrl,
                ]
            ]
        ];
    }
}
