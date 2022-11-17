<?php

namespace App\Contribution\Documents;

use Zoomyboy\Tex\Document;

abstract class ContributionDocument extends Document
{
    abstract public static function getName(): string;
}
