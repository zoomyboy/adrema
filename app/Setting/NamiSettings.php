<?php

namespace App\Setting;

use App\Group;
use Spatie\LaravelSettings\Settings;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Nami;

class NamiSettings extends Settings
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
}
