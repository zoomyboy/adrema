<?php

namespace App\Fileshare;

use App\Fileshare\Models\Fileshare;
use App\Fileshare\Resources\FileshareResource;
use App\Setting\Contracts\Viewable;
use App\Setting\LocalSettings;

class FileshareSettings extends LocalSettings implements Viewable
{
    public static function group(): string
    {
        return 'fileshare';
    }

    public static function slug(): string
    {
        return 'fileshare';
    }

    public static function title(): string
    {
        return 'Datei-Verbindungen';
    }

    /**
     * @inheritdoc
     */
    public function viewData(): array
    {
        return [
            'data' => FileshareResource::collection(Fileshare::paginate(15))
        ];
    }
}
