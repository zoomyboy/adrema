<?php

namespace App\Mailgateway;

use App\Mailgateway\Actions\IndexAction;
use App\Setting\Contracts\Indexable;
use App\Setting\LocalSettings;

class MailgatewaySettings extends LocalSettings implements Indexable
{
    public static function group(): string
    {
        return 'mailgateway';
    }

    public static function slug(): string
    {
        return 'mailgateway';
    }

    public static function indexAction(): string
    {
        return IndexAction::class;
    }

    public static function title(): string
    {
        return 'E-Mail-Verbindungen';
    }
}
