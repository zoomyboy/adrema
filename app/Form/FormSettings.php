<?php

namespace App\Form;

use App\Form\Actions\SettingIndexAction;
use App\Form\Actions\SettingStoreAction;
use App\Setting\Contracts\Indexable;
use App\Setting\Contracts\Storeable;
use App\Setting\LocalSettings;

class FormSettings extends LocalSettings implements Indexable, Storeable
{
    public string $registerUrl;

    public static function group(): string
    {
        return 'form';
    }

    public static function slug(): string
    {
        return 'form';
    }

    public static function title(): string
    {
        return 'Formulare';
    }

    public static function indexAction(): string
    {
        return SettingIndexAction::class;
    }

    public static function storeAction(): string
    {
        return SettingStoreAction::class;
    }
}
