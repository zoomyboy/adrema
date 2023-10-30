<?php

namespace App\Setting;

use App\Group;
use App\Nami\Actions\SettingIndexAction;
use App\Nami\Actions\SettingSaveAction;
use App\Setting\Contracts\Indexable;
use App\Setting\Contracts\Storeable;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Nami;

class NamiSettings extends LocalSettings implements Indexable, Storeable
{
    public int $mglnr;

    public string $password;

    public int $default_group_id;

    /** @var array<string, string> */
    public array $search_params;

    public static function group(): string
    {
        return 'nami';
    }

    public function login(): Api
    {
        return Nami::login($this->mglnr, $this->password);
    }

    public function localGroup(): ?Group
    {
        return Group::firstWhere('nami_id', $this->default_group_id);
    }

    public static function slug(): string
    {
        return 'nami';
    }

    public static function indexAction(): string
    {
        return SettingIndexAction::class;
    }

    public static function storeAction(): string
    {
        return SettingSaveAction::class;
    }

    public static function title(): string
    {
        return 'NaMi-Login';
    }
}
