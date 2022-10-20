<?php

namespace App\Mailman;

use App\Mailman\Actions\SettingIndexAction;
use App\Mailman\Actions\SettingSaveAction;
use App\Setting\LocalSettings;

class MailmanSettings extends LocalSettings
{
    public ?string $base_url;

    public ?string $username;

    public ?string $password;

    public bool $is_active;

    public ?string $all_list;

    public ?string $all_parents_list;

    public ?string $active_leaders_list;

    public ?string $passive_leaders_list;

    public static function group(): string
    {
        return 'mailman';
    }

    public static function slug(): string
    {
        return 'mailman';
    }

    public static function indexAction(): string
    {
        return SettingIndexAction::class;
    }

    public static function saveAction(): string
    {
        return SettingSaveAction::class;
    }

    public static function title(): string
    {
        return 'Mailman';
    }
}
