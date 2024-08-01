<?php

namespace App\Mailgateway;

use App\Mailgateway\Actions\IndexAction;
use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Resources\MailgatewayResource;
use App\Setting\Contracts\Viewable;
use App\Setting\LocalSettings;

class MailgatewaySettings extends LocalSettings implements Viewable
{
    public static function group(): string
    {
        return 'mailgateway';
    }

    public static function slug(): string
    {
        return 'mailgateway';
    }

    public static function title(): string
    {
        return 'E-Mail-Verbindungen';
    }

    /**
     * @inheritdoc
     */
    public function viewData(): array
    {
        return [
            'data' => MailgatewayResource::collection(Mailgateway::paginate(10)),
        ];
    }
}
