<?php

namespace App\Fileshare\Data;

use Spatie\LaravelData\Data;

class ResourceData extends Data
{

    public function __construct(public string $name, public string $path, public string $parent)
    {
    }

    public static function fromString(string $path): self
    {
        $dir = '/' . trim($path, '\\/');

        return self::from([
            'path' => $dir,
            'name' => pathinfo($dir, PATHINFO_BASENAME),
            'parent' => pathinfo($dir, PATHINFO_DIRNAME),
        ]);
    }
}
