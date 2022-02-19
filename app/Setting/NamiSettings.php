<?php

namespace App\Setting;

use Spatie\LaravelSettings\Settings;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Nami;

class NamiSettings extends Settings
{

    public int $mglnr;

    public string $password;

    public static function group(): string
    {
        return 'nami';
    }

    public function login(): Api
    {
        return Nami::login($this->mglnr, $this->password);
    }

}
